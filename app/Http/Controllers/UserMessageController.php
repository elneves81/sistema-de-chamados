<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserMessageController extends Controller
{
        /**
     * Show the form for composing a new message (Admin only).
     */
    public function compose(User $user = null)
    {
        // Verificar se é administrador
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Apenas administradores podem enviar mensagens.');
        }
        
        $users = User::where('id', '!=', auth()->id())
                    ->orderBy('name')
                    ->get();
        
        return view('messages.compose', compact('users', 'user'));
    }
    
    /**
     * Display a listing of the user messages.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Mensagens recebidas
        $receivedMessages = UserMessage::with(['fromUser'])
            ->forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'received');

        // Mensagens enviadas
        $sentMessages = UserMessage::with(['toUser'])
            ->fromUser($user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'sent');

        // Contagem de não lidas
        $unreadCount = UserMessage::forUser($user->id)->unread()->count();

        return view('messages.index', compact('receivedMessages', 'sentMessages', 'unreadCount'));
    }

    /**
     * Exibe uma mensagem específica
     */
    public function show(UserMessage $message)
    {
        $user = Auth::user();

        // Verificar se o usuário pode ver esta mensagem
        if ($message->to_user_id !== $user->id && $message->from_user_id !== $user->id) {
            abort(403, 'Você não tem permissão para ver esta mensagem.');
        }

        // Marcar como lida se for o destinatário
        if ($message->to_user_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Exibe formulário para nova mensagem (para administradores)
     */
    public function create(Request $request)
    {
        // Verificar se é administrador
        if (!in_array(Auth::user()->role, ['admin', 'technician'])) {
            abort(403, 'Apenas administradores podem enviar mensagens.');
        }

        $users = User::where('id', '!=', Auth::id())
                    ->orderBy('name')
                    ->get();

        $selectedUserId = $request->get('user_id');

        return view('messages.create', compact('users', 'selectedUserId'));
    }

    /**
     * Envia uma nova mensagem
     */
    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $message = UserMessage::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority
        ]);

        // Enviar email de notificação
        $this->sendEmailNotification($message);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso!',
                'data' => [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'to_user' => $message->toUser->name
                ]
            ]);
        }

        return redirect()->route('messages.index')
                        ->with('success', 'Mensagem enviada com sucesso!');
    }

    /**
     * Responder uma mensagem
     */
    public function reply(Request $request, $messageId)
    {
        try {
            // Buscar a mensagem original
            $originalMessage = UserMessage::findOrFail($messageId);
            
            Log::info('Reply attempt', [
                'user_id' => Auth::id(),
                'original_message_id' => $originalMessage->id,
                'from_user_id' => $originalMessage->from_user_id,
                'to_user_id' => $originalMessage->to_user_id,
                'request_data' => $request->all()
            ]);

            // Verificar se o usuário pode responder esta mensagem
            if ($originalMessage->to_user_id !== Auth::id() && $originalMessage->from_user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para responder esta mensagem.'
                ], 403);
            }

            $request->validate([
                'message' => 'required|string|max:5000'
            ]);

            // Determinar o destinatário da resposta
            $replyToUserId = ($originalMessage->from_user_id === Auth::id()) 
                ? $originalMessage->to_user_id 
                : $originalMessage->from_user_id;

            $replyMessage = UserMessage::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $replyToUserId,
                'subject' => 'Re: ' . $originalMessage->subject,
                'message' => $request->message,
                'priority' => $originalMessage->priority
            ]);

            Log::info('Reply message created', [
                'reply_id' => $replyMessage->id,
                'reply_to_user_id' => $replyToUserId
            ]);

            // Enviar email de notificação
            $this->sendEmailNotification($replyMessage);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Resposta enviada com sucesso!'
                ]);
            }

            return redirect()->route('messages.show', $originalMessage)
                            ->with('success', 'Resposta enviada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Error in reply method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Erro ao enviar resposta: ' . $e->getMessage());
        }
    }

    /**
     * Marcar mensagem como lida
     */
    public function markAsRead(UserMessage $message)
    {
        if ($message->to_user_id !== Auth::id()) {
            abort(403);
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marcar todas as mensagens como lidas
     */
    public function markAllAsRead()
    {
        UserMessage::forUser(Auth::id())
                  ->unread()
                  ->update([
                      'is_read' => true,
                      'read_at' => now()
                  ]);

        return response()->json(['success' => true]);
    }

    /**
     * Obter contagem de mensagens não lidas
     */
    public function getUnreadCount()
    {
        $count = UserMessage::forUser(Auth::id())->unread()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * API: Obter mensagens recentes para notificação
     */
    public function recent()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não autenticado',
                    'messages' => [],
                    'unread_count' => 0
                ], 401);
            }
            
            $messages = UserMessage::with(['fromUser'])
                ->forUser($user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($message) {
                    return [
                        'id' => $message->id,
                        'subject' => $message->subject,
                        'message' => $message->message,
                        'from_user' => [
                            'name' => $message->fromUser ? $message->fromUser->name : 'Usuário desconhecido',
                            'email' => $message->fromUser ? $message->fromUser->email : '',
                        ],
                        'is_read' => $message->is_read,
                        'priority' => $message->priority,
                        'time_ago' => $message->created_at->diffForHumans(),
                        'created_at' => $message->created_at->format('d/m/Y H:i'),
                    ];
                });
            
            $unreadCount = UserMessage::forUser($user->id)->unread()->count();
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'unread_count' => $unreadCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar mensagens: ' . $e->getMessage(),
                'messages' => [],
                'unread_count' => 0
            ], 500);
        }
    }

    /**
     * API: Contar mensagens não lidas
     */
    public function unreadCount()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não autenticado',
                    'count' => 0
                ], 401);
            }
            
            $count = UserMessage::forUser($user->id)->unread()->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao contar mensagens: ' . $e->getMessage(),
                'count' => 0
            ], 500);
        }
    }

    /**
     * API: Listar usuários para envio de mensagem (para administradores)
     */
    public function getUsersForMessage()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Usuário não autenticado',
                    'users' => []
                ], 401);
            }
            
            if (!in_array($user->role, ['admin', 'technician'])) {
                return response()->json([
                    'error' => 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.',
                    'users' => []
                ], 403);
            }

            $users = User::where('id', '!=', $user->id)
                        ->select('id', 'name', 'email', 'role')
                        ->orderBy('name')
                        ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar usuários: ' . $e->getMessage(),
                'users' => []
            ], 500);
        }
    }

    /**
     * Enviar notificação por email
     */
    private function sendEmailNotification(UserMessage $message)
    {
        try {
            $toUser = $message->toUser;
            
            // Aqui você implementaria o envio real do email
            // Por enquanto, vou simular o envio
            Log::info("Email enviado para {$toUser->email}: {$message->subject}");
            
            $message->markEmailSent();
            
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de notificação: ' . $e->getMessage());
        }
    }
}
