<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class KnowledgeBaseController extends Controller
{
    /**
     * Lista inicial (com cache) + destaques e populares.
     */
    public function index()
    {
        // 10 min de cache; ajuste conforme necessidade
        $categories = Cache::remember('kb:categories_with_top3', 600, function () {
            return KnowledgeCategory::active()
                ->with(['publishedArticles' => function ($query) {
                    $query->latest('published_at')->take(3);
                }])
                ->ordered()
                ->get();
        });

        $featuredArticles = Cache::remember('kb:featured_top3', 600, function () {
            return KnowledgeArticle::published()->public()
                ->featured()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        // Se o escopo popular() já ordena por views, ok; se não, reforçamos:
        $popularArticles = Cache::remember('kb:popular_top5', 600, function () {
            return KnowledgeArticle::published()->public()
                ->popular() // ideal que ordene por views desc
                ->with(['category', 'author'])
                ->take(5)
                ->get();
        });

        return view('knowledge.index', compact('categories', 'featuredArticles', 'popularArticles'));
    }

    /**
     * Lista artigos de uma categoria (só ativa).
     */
    public function category(KnowledgeCategory $category)
    {
        abort_if(method_exists($category, 'getAttribute') && !$category->is_active, 404);

        $articles = $category->publishedArticles()
            ->with(['author'])
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('knowledge.category', compact('category', 'articles'));
    }

    /**
     * Form de criação.
     */
    public function create()
    {
        $this->authorize('create', KnowledgeArticle::class);

        $categories = KnowledgeCategory::active()->ordered()->get();
        return view('knowledge.create', compact('categories'));
    }

    /**
     * Persistência de novo artigo.
     */
    public function store(Request $request)
    {
        $this->authorize('create', KnowledgeArticle::class);

        $validated = $this->validateArticle($request);

        // Normalizar checkboxes (evita bug quando desmarcados não vêm no payload)
        $validated['is_public']   = $request->boolean('is_public');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Tags: aceitar string "a,b,c" e transformar em array
        $validated['tags'] = $this->normalizeTags($validated['tags'] ?? null);

        // Excerpt automático, se vazio
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 200);
        }

        $validated['author_id'] = Auth::id();

        if (($validated['status'] ?? 'draft') === 'published') {
            $validated['published_at'] = now();
        }

        // (Opcional) Sanitizar HTML se conteúdo for rico:
        // $validated['content'] = \Purifier::clean($validated['content']);

        $article = KnowledgeArticle::create($validated);

        // Limpar caches relacionados
        Cache::forget('kb:featured_top3');
        Cache::forget('kb:popular_top5');
        Cache::forget('kb:categories_with_top3');

        return redirect()->route('knowledge.show', $article)
            ->with('success', 'Artigo criado com sucesso.');
    }

    /**
     * Visualização de artigo (respeitando visibilidade).
     */
    public function show(KnowledgeArticle $article)
    {
        // Se não for público/publicado, permitir só autor ou quem tem permissão
        if ($article->status !== 'published' || !$article->is_public) {
            $this->authorize('view', $article);
        }

        // Incremento atômico (ideal ter método que faça throttling por IP/sessão)
        // $article->incrementViews();
        $article->increment('views');

        $article->load('category', 'author');

        $relatedArticles = KnowledgeArticle::published()->public()
            ->where('id', '<>', $article->id)
            ->where('category_id', $article->category_id)
            ->popular()
            ->take(3)
            ->get();

        return view('knowledge.show', compact('article', 'relatedArticles'));
    }

    /**
     * Form de edição.
     */
    public function edit(KnowledgeArticle $article)
    {
        $this->authorize('update', $article);

        $categories = KnowledgeCategory::active()->ordered()->get();

        // Formatar tags para o input text
        $article->tags_string = is_array($article->tags) ? implode(', ', $article->tags) : ($article->tags ?? '');

        return view('knowledge.edit', compact('article', 'categories'));
    }

    /**
     * Atualização de artigo.
     */
    public function update(Request $request, KnowledgeArticle $article)
    {
        $this->authorize('update', $article);

        $validated = $this->validateArticle($request);

        $validated['is_public']   = $request->boolean('is_public');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['tags']        = $this->normalizeTags($validated['tags'] ?? null);

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 200);
        }

        // Se mudou de rascunho para publicado, defina published_at
        if (($validated['status'] ?? $article->status) === 'published' && $article->status !== 'published') {
            $validated['published_at'] = now();
        }
        // (Opcional) Se voltou a draft, você pode limpar published_at:
        // if (($validated['status'] ?? $article->status) === 'draft' && $article->status === 'published') {
        //     $validated['published_at'] = null;
        // }

        $article->update($validated);

        Cache::forget('kb:featured_top3');
        Cache::forget('kb:popular_top5');
        Cache::forget('kb:categories_with_top3');

        return redirect()->route('knowledge.show', $article)
            ->with('success', 'Artigo atualizado com sucesso.');
    }

    /**
     * Exclusão (ideal usar SoftDeletes no model).
     */
    public function destroy(KnowledgeArticle $article)
    {
        $this->authorize('delete', $article);

        $article->delete();

        Cache::forget('kb:featured_top3');
        Cache::forget('kb:popular_top5');
        Cache::forget('kb:categories_with_top3');

        return redirect()->route('knowledge.index')
            ->with('success', 'Artigo excluído com sucesso.');
    }

    /**
     * Busca (LIKE seguro + paginação com query string).
     */
    public function search(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        // Evita retornar "tudo" se consulta vazia
        if ($query === '') {
            return view('knowledge.search', [
                'articles' => collect(), // vazio
                'query' => $query,
            ]);
        }

        // Escapar curingas do LIKE
        $escaped = addcslashes($query, '%_');

        $articles = KnowledgeArticle::published()->public()
            ->where(function ($q) use ($escaped) {
                $q->where('title', 'like', "%{$escaped}%")
                  ->orWhere('content', 'like', "%{$escaped}%")
                  ->orWhere('excerpt', 'like', "%{$escaped}%");
            })
            ->with(['category', 'author'])
            ->orderByDesc('views')
            ->paginate(10)
            ->withQueryString();

        return view('knowledge.search', compact('articles', 'query'));
    }

    /**
     * --------- Helpers internos ----------
     */

    private function validateArticle(Request $request): array
    {
        // Dica: mover para FormRequest dedicado (ex.: StoreKnowledgeArticleRequest / UpdateKnowledgeArticleRequest)
        return $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'excerpt'     => 'nullable|string|max:500',
            'category_id' => 'required|exists:knowledge_categories,id',
            // Permitimos string (campo de formulário) ou array (API); o normalizador converte depois
            'tags'        => 'nullable',
            'status'      => 'required|in:draft,published',
            // booleans tratados com $request->boolean()
            'is_public'   => 'nullable',
            'is_featured' => 'nullable',
        ]);
    }

    private function normalizeTags($tags): array
    {
        if (is_array($tags)) {
            return array_values(array_filter(array_map(fn ($t) => trim((string) $t), $tags)));
        }

        if (is_string($tags) && trim($tags) !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return [];
    }
}
