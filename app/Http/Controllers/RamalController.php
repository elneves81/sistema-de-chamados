<?php

namespace App\Http\Controllers;

use App\Models\Ramal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RamalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ramal::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('departamento', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%")
                  ->orWhere('ramal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departamento')) {
            $query->byDepartamento($request->departamento);
        }

        $ramais = $query->orderBy('departamento', 'asc')->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Ramal::count(),
        ];

        return view('ramais.index', compact('ramais', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar se o usuário tem permissão para criar (admin ou técnico)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Você não tem permissão para criar ramais.');
        }

        return view('ramais.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar se o usuário tem permissão para criar (admin ou técnico)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Você não tem permissão para criar ramais.');
        }

        $validated = $request->validate([
            'departamento' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'ramal' => 'required|string|max:50',
        ]);

        Ramal::create($validated);

        return redirect()->route('ramais.index')
            ->with('success', 'Ramal criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ramal $ramal)
    {
        return view('ramais.show', compact('ramal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ramal $ramal)
    {
        // Verificar se o usuário tem permissão para editar (admin ou técnico)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Você não tem permissão para editar ramais.');
        }

        return view('ramais.edit', compact('ramal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ramal $ramal)
    {
        // Verificar se o usuário tem permissão para editar (admin ou técnico)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Você não tem permissão para editar ramais.');
        }

        $validated = $request->validate([
            'departamento' => 'required|string|max:255',
            'descricao' => 'required|string|max:255',
            'ramal' => 'required|string|max:50',
        ]);

        $ramal->update($validated);

        return redirect()->route('ramais.index')
            ->with('success', 'Ramal atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ramal $ramal)
    {
        // Verificar se o usuário tem permissão para excluir (admin ou técnico)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Você não tem permissão para excluir ramais.');
        }

        $ramal->delete();

        return redirect()->route('ramais.index')
            ->with('success', 'Ramal excluído com sucesso!');
    }
}
