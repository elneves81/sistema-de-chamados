<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Middleware de permissão removido - já está nas rotas
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('tickets')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'active' => 'boolean',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['tickets' => function($query) {
            $query->with(['user', 'assignedUser'])
                  ->orderBy('created_at', 'desc')
                  ->limit(10);
        }]);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'active' => 'boolean',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar se há tickets associados
        if ($category->tickets()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir uma categoria que possui chamados associados.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
