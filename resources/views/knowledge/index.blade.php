@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-book mr-2"></i>Base de Conhecimento
                </h1>
                @auth
                    <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Novo Artigo
                    </a>
                @endauth
            </div>

            <!-- Barra de Pesquisa -->
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <form method="GET" action="{{ route('knowledge.search') }}" class="position-relative">
                        <input type="text" name="q" class="form-control form-control-lg pl-5" 
                               placeholder="Pesquisar artigos..." 
                               value="{{ request('q') }}">
                        <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>
                        <button type="submit" class="btn btn-primary position-absolute" 
                                style="right: 5px; top: 5px; bottom: 5px;">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>

            @if($featuredArticles->count() > 0)
            <!-- Artigos em Destaque -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="fas fa-star text-warning mr-2"></i>Artigos em Destaque
                    </h4>
                    <div class="row">
                        @foreach($featuredArticles as $article)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-left-warning">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('knowledge.show', $article) }}" class="text-decoration-none">
                                            {{ $article->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">{{ $article->short_excerpt }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge badge-info">{{ $article->category->name }}</span>
                                        <small class="text-muted">
                                            <i class="fas fa-eye mr-1"></i>{{ $article->views }} visualizações
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Categorias -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="fas fa-folder mr-2"></i>Categorias
                    </h4>
                    <div class="row">
                        @foreach($categories as $category)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-left" style="border-left-color: {{ $category->color }}!important;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="{{ $category->icon ?? 'fas fa-folder' }} fa-2x mr-3" 
                                           style="color: {{ $category->color }};"></i>
                                        <div>
                                            <h5 class="card-title mb-0">
                                                <a href="{{ route('knowledge.category', $category) }}" class="text-decoration-none">
                                                    {{ $category->name }}
                                                </a>
                                            </h5>
                                            <small class="text-muted">{{ $category->article_count }} artigos</small>
                                        </div>
                                    </div>
                                    @if($category->description)
                                    <p class="card-text text-muted small">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($popularArticles->count() > 0)
            <!-- Artigos Populares -->
            <div class="row">
                <div class="col-12">
                    <h4 class="mb-3">
                        <i class="fas fa-fire text-danger mr-2"></i>Artigos Populares
                    </h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($popularArticles as $article)
                                <div class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">
                                            <a href="{{ route('knowledge.show', $article) }}" class="text-decoration-none">
                                                {{ $article->title }}
                                            </a>
                                        </div>
                                        <small class="text-muted">
                                            <span class="badge badge-secondary mr-1">{{ $article->category->name }}</span>
                                            Por {{ $article->author->name }} • 
                                            {{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <span class="badge badge-primary badge-pill">
                                        <i class="fas fa-eye mr-1"></i>{{ $article->views }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
