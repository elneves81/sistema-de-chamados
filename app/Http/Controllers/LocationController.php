<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,technician')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Location::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $locations = $query->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'comment' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Evitar inserir NULL em colunas com default no banco (ex.: country)
        if (!isset($validated['country']) || $validated['country'] === null || $validated['country'] === '') {
            // Se o schema definir default('Brasil'), podemos setar explicitamente para manter consistência
            $validated['country'] = 'Brasil';
        }

        // Remover somente valores nulos, preservando 0/false
        $validated = array_filter($validated, function ($v) { return !is_null($v); });

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Localização criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['users', 'assets']);
        
        return view('admin.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'comment' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        // No update, se o campo 'country' vier vazio/nulo, não sobrescrever o valor existente
        if (array_key_exists('country', $validated) && ($validated['country'] === null || $validated['country'] === '')) {
            unset($validated['country']);
        }

        // Remover somente valores nulos, preservando 0/false
        $validated = array_filter($validated, function ($v) { return !is_null($v); });

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        // Verificar se há usuários ou ativos vinculados
        if ($location->users()->count() > 0 || $location->tickets()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Não é possível excluir esta localização pois há usuários ou tickets vinculados.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Localização excluída com sucesso!');
    }
}
