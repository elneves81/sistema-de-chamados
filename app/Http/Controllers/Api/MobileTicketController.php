<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MobileTicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['category', 'location', 'assignedUser', 'user', 'asset'])
            ->orderByDesc('updated_at');

        // Técnicos veem por padrão tickets atribuídos a eles; admin pode ver todos
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->input('status'));
        }
        if ($request->filled('priority')) {
            $query->whereIn('priority', (array) $request->input('priority'));
        }

        $tickets = $query->paginate($request->integer('per_page', 20));
        return response()->json($tickets);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['category', 'location', 'assignedUser', 'user', 'comments.user', 'asset']);
        $user = $request->user();
        if ($user->role !== 'admin' && !in_array($user->id, [$ticket->assigned_to, $ticket->user_id])) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        return response()->json($ticket);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting,resolved,closed',
        ]);

        $user = $request->user();
        if ($user->role !== 'admin' && $user->role !== 'technician') {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $new = $request->input('status');
        $ticket->status = $new;
        if ($new === 'in_progress' && !$ticket->assigned_to) {
            $ticket->assigned_to = $user->id;
        }
        if ($new === 'resolved') {
            $ticket->resolved_at = now();
            $ticket->resolved_by = $user->id;
        }
        if ($new === 'closed') {
            $ticket->closed_at = now();
            $ticket->closed_by = $user->id;
        }
        $ticket->save();

        return response()->json(['success' => true, 'ticket' => $ticket->fresh()]);
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $user = $request->user();
        if ($user->role !== 'admin' && !in_array($user->id, [$ticket->assigned_to, $ticket->user_id])) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if (!$request->filled('comment') && !$request->hasFile('attachments')) {
            return response()->json(['message' => 'Informe um comentário ou selecione pelo menos um arquivo.'], 422);
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $request->input('comment', ''),
            'is_internal' => false,
        ]);

        if ($request->hasFile('attachments')) {
            $stored = [];
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("tickets/{$ticket->id}/comments/{$comment->id}", 'public');
                    $stored[] = [
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_by' => $user->id,
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            if (!empty($stored)) {
                $comment->attachments = $stored;
                $comment->save();
            }
        }

        return response()->json(['success' => true, 'comment' => $comment->fresh()]);
    }

    public function claim(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ($user->role !== 'admin' && $user->role !== 'technician') {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        // Se já atribuído a outro usuário, impedir claim concorrente
        if ($ticket->assigned_to && $ticket->assigned_to !== $user->id) {
            $ticket->load('assignedUser');
            return response()->json([
                'message' => 'Chamado já atribuído',
                'assigned_to' => [
                    'id' => $ticket->assignedUser?->id,
                    'name' => $ticket->assignedUser?->name,
                ]
            ], 409);
        }

        // Atribuir ao usuário atual e, se status ainda "open", colocar em "in_progress"
        $ticket->assigned_to = $user->id;
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }
        $ticket->save();

        return response()->json(['success' => true, 'ticket' => $ticket->fresh(['assignedUser'])], 200);
    }
}
