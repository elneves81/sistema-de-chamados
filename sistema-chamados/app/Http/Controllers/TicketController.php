<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'category', 'assignedUser'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por usuário baseado no role
        $user = Auth::user();
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'technician') {
            $query->where('assigned_to', $user->id);
        }

        $tickets = $query->paginate(15);
        $categories = Category::all();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('tickets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'local' => 'nullable|string|max:255',
        ]);

        $ticket = Ticket::create([
            'title' => $request->title,
            'local' => $request->local,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'user_id' => Auth::id(),
            'status' => 'open',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Chamado criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // Verificar permissão
        $user = Auth::user();
        if ($user->role === 'customer' && $ticket->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        $ticket->load(['user', 'category', 'assignedUser', 'comments.user']);
        
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        // Apenas admin e técnico podem editar
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado.');
        }

        $categories = Category::where('active', true)->get();
        $technicians = \App\Models\User::where('role', 'technician')->get();

        return view('tickets.edit', compact('ticket', 'categories', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Validação baseada no role
        $rules = [];
        if (in_array($user->role, ['admin', 'technician'])) {
            $rules = [
                'status' => 'required|in:open,in_progress,waiting,resolved,closed',
                'priority' => 'required|in:low,medium,high,urgent',
                'assigned_to' => 'nullable|exists:users,id',
                'category_id' => 'required|exists:categories,id',
            ];
            
            if ($user->role === 'admin') {
                $rules['title'] = 'required|string|max:255';
                $rules['description'] = 'required|string';
            }
        }

        $request->validate($rules);

        $updateData = $request->only(array_keys($rules));
        $ticket->update($updateData);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Chamado atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Apenas admin pode deletar
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Chamado removido com sucesso!');
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comentário adicionado com sucesso!');
    }
}
