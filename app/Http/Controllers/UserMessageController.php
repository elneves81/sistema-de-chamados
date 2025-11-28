<?php

namespace App\Http\Controllers;

use App\Mail\UserMessageNotification;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserMessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $receivedMessages = UserMessage::with('fromUser')
            ->where('to_user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $sentMessages = UserMessage::with('toUser')
            ->where('from_user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $unreadCount = UserMessage::where('to_user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('messages.index', compact('receivedMessages', 'sentMessages', 'unreadCount'));
    }

    public function compose(Request $request)
    {
        $this->authorizeCompose();
        // Não carregar 5k+ usuários aqui; a view usa busca sob demanda
        return view('messages.compose');
    }

    public function store(Request $request)
    {
        $this->authorizeCompose();

        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'send_email' => 'nullable|boolean',
        ]);

        $message = new UserMessage();
        $message->from_user_id = Auth::id();
        $message->to_user_id = (int) $validated['to_user_id'];
        $message->subject = $validated['subject'];
        $message->message = $validated['message'];
        $message->priority = $validated['priority'] ?? 'medium';
        $message->is_read = false;
        $message->save();

        if ($request->boolean('send_email')) {
            try {
                $to = User::find($message->to_user_id);
                if ($to && $to->email) {
                    Mail::to($to->email)->queue(new UserMessageNotification($message));
                    $message->markEmailSent();
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao enviar email de notificação de mensagem', [
                    'message_id' => $message->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'id' => $message->id]);
        }

        return redirect()->route('messages.show', $message->id)
            ->with('status', 'Mensagem enviada com sucesso!');
    }

    public function show($id)
    {
        $message = UserMessage::with(['fromUser', 'toUser'])->findOrFail($id);
        $userId = Auth::id();

        if ($message->from_user_id !== $userId && $message->to_user_id !== $userId) {
            abort(403);
        }

        if ($message->to_user_id === $userId && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    public function reply(Request $request, $id)
    {
        $original = UserMessage::findOrFail($id);
        $userId = Auth::id();

        if ($original->to_user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $reply = new UserMessage();
        $reply->from_user_id = $userId;
        $reply->to_user_id = $original->from_user_id;
        $reply->subject = str_starts_with($original->subject, 'Re: ')
            ? $original->subject
            : 'Re: ' . $original->subject;
        $reply->message = $data['message'];
        $reply->priority = $original->priority;
        $reply->is_read = false;
        $reply->save();

        try {
            $to = User::find($reply->to_user_id);
            if ($to && $to->email) {
                Mail::to($to->email)->queue(new UserMessageNotification($reply));
                $reply->markEmailSent();
            }
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar email de notificação de resposta', [
                'message_id' => $reply->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        $count = UserMessage::where('to_user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true, 'updated' => $count]);
    }

    // Marcar mensagem específica como lida
    public function markAsRead(UserMessage $message)
    {
        if ($message->to_user_id !== Auth::id()) {
            abort(403);
        }
        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    public function ajaxUsers(Request $request)
    {
        $this->authorizeCompose();

        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min($limit, 50));

        $currentUser = Auth::user();
        
        $query = User::query()
            ->where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'username', 'role')
            ->orderBy('name');

        // Se o usuário for customer, mostrar apenas admins e técnicos
        if ($currentUser->role === 'customer') {
            $query->whereIn('role', ['admin', 'technician']);
        }

        if ($q !== '') {
            $qLike = '%' . $q . '%';
            $qStart = $q . '%';
            // Priorizar início de palavra no nome
            $query->where(function ($sub) use ($qLike, $qStart) {
                $sub->where('name', 'like', $qLike)
                    ->orWhere('email', 'like', $qLike)
                    ->orWhere('username', 'like', $qLike);
            })
            ->orderByRaw('(name LIKE ?) DESC, (name LIKE ?) DESC', [$qStart, '% ' . $qStart])
            ->orderBy('name');
        }

        $users = $query->limit($limit)->get();

        return response()->json(['users' => $users]);
    }

    public function ajaxUnreadCount(Request $request)
    {
        $count = UserMessage::where('to_user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // Compatibilidade com rotas existentes
    public function unreadCount()
    {
        return $this->ajaxUnreadCount(request());
    }

    public function getUsersForMessage()
    {
        return $this->ajaxUsers(request());
    }

    public function recent()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado', 'messages' => [], 'unread_count' => 0], 401);
        }

        $messages = UserMessage::with(['fromUser'])
            ->where('to_user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'message' => $message->message,
                    'from_user' => [
                        'name' => optional($message->fromUser)->name,
                        'email' => optional($message->fromUser)->email,
                    ],
                    'is_read' => $message->is_read,
                    'priority' => $message->priority,
                    'time_ago' => $message->created_at?->diffForHumans(),
                    'created_at' => $message->created_at?->format('d/m/Y H:i'),
                ];
            });

        $unreadCount = UserMessage::where('to_user_id', $user->id)->where('is_read', false)->count();
        return response()->json(['success' => true, 'messages' => $messages, 'unread_count' => $unreadCount]);
    }

    private function authorizeCompose(): void
    {
        $user = Auth::user();
        $can = false;

        if (method_exists($user, 'hasPermission')) {
            $can = $user->hasPermission('users.manage');
        }

        if (!$can) {
            $can = in_array($user->role ?? null, ['admin', 'technician']);
        }

        if (!$can) {
            abort(403, 'Você não tem permissão para enviar mensagens.');
        }
    }
}

