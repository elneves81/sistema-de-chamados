@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('knowledge.index') }}">Base de Conhecimento</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('knowledge.category', $article->category->slug) }}">{{ $article->category->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $article->title }}</li>
                </ol>
            </nav>

            <!-- Artigo -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h4 mb-2">{{ $article->title }}</h1>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="badge badge-light mr-2">{{ $article->category->name }}</span>
                                @if($article->is_featured)
                                    <span class="badge badge-warning mr-2">
                                        <i class="fas fa-star mr-1"></i>Destaque
                                    </span>
                                @endif
                                <small class="text-light mr-3">
                                    <i class="fas fa-user mr-1"></i>{{ $article->author->name }}
                                </small>
                                <small class="text-light mr-3">
                                    <i class="fas fa-calendar mr-1"></i>{{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                </small>
                                <small class="text-light">
                                    <i class="fas fa-eye mr-1"></i>{{ $article->views }} visualizações
                                </small>
                            </div>
                        </div>
                        @auth
                            @if(auth()->user()->role === 'admin' || auth()->user()->id === $article->author_id)
                            <div class="dropdown">
                                <button class="btn btn-outline-light btn-sm" type="button" data-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('admin.knowledge.edit', $article) }}">
                                        <i class="fas fa-edit mr-2"></i>Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.knowledge.destroy', $article) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger" 
                                                onclick="return confirm('Tem certeza que deseja excluir este artigo?')">
                                            <i class="fas fa-trash mr-2"></i>Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="card-body">
                    @if($article->excerpt)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>{{ $article->excerpt }}
                    </div>
                    @endif

                    <div class="article-content">
                        {!! $article->content !!}
                    </div>

                    @if($article->tags && count($article->tags) > 0)
                    <hr>
                    <div class="mb-3">
                        <strong>Tags:</strong>
                        @foreach($article->tags as $tag)
                            <span class="badge badge-secondary mr-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif

                    @if($article->rating > 0)
                    <div class="mb-3">
                        <strong>Avaliação:</strong>
                        <div class="d-inline-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $article->rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="ml-2 text-muted">({{ $article->rating_count }} avaliações)</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($relatedArticles->count() > 0)
            <!-- Artigos Relacionados -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-link mr-2"></i>Artigos Relacionados
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($relatedArticles as $related)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <h6 class="mb-1">
                            <a href="{{ route('knowledge.show', $related) }}" class="text-decoration-none">
                                {{ $related->title }}
                            </a>
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-eye mr-1"></i>{{ $related->views }} visualizações
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Navegação da Categoria -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-folder mr-2"></i>{{ $article->category->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ $article->category->description }}</p>
                    <a href="{{ route('knowledge.category', $article->category) }}" class="btn btn-outline-primary btn-sm">
                        Ver todos os artigos desta categoria
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.article-content {
    line-height: 1.6;
}

.article-content h1, 
.article-content h2, 
.article-content h3, 
.article-content h4, 
.article-content h5, 
.article-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.article-content ul, 
.article-content ol {
    margin-bottom: 1rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.25rem;
    margin: 1rem 0;
}

.article-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.article-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.25rem;
    overflow-x: auto;
}
</style>
@endsection
