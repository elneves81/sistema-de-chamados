@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('knowledge.index') }}">Base de Conhecimento</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>

            <!-- Cabeçalho da Categoria -->
            <div class="card border-left" style="border-left-color: {{ $category->color }}!important;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <i class="{{ $category->icon ?? 'fas fa-folder' }} fa-3x mr-3" 
                                   style="color: {{ $category->color }};"></i>
                                <div>
                                    <h1 class="h3 mb-0">{{ $category->name }}</h1>
                                    <p class="text-muted mb-0">{{ $category->description }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <div class="d-flex flex-column align-items-md-end">
                                <span class="badge badge-primary badge-pill mb-2">
                                    {{ $articles->total() }} artigos
                                </span>
                                @auth
                                    <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus mr-1"></i>Novo Artigo
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Artigos -->
            @if($articles->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list mr-2"></i>Artigos da Categoria
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($articles as $article)
                            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="mb-2">
                                            <a href="{{ route('knowledge.show', $article) }}" class="text-decoration-none">
                                                {{ $article->title }}
                                                @if($article->is_featured)
                                                    <span class="badge badge-warning ml-2">
                                                        <i class="fas fa-star mr-1"></i>Destaque
                                                    </span>
                                                @endif
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-2">{{ $article->short_excerpt }}</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <small class="text-muted mr-3">
                                                <i class="fas fa-user mr-1"></i>{{ $article->author->name }}
                                            </small>
                                            <small class="text-muted mr-3">
                                                <i class="fas fa-calendar mr-1"></i>{{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-eye mr-1"></i>{{ $article->views }} visualizações
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right">
                                        @if($article->tags && count($article->tags) > 0)
                                        <div class="mb-2">
                                            @foreach(array_slice($article->tags, 0, 3) as $tag)
                                                <span class="badge badge-secondary mr-1">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($article->tags) > 3)
                                                <span class="badge badge-light">+{{ count($article->tags) - 3 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        @if($article->rating > 0)
                                        <div class="mb-2">
                                            <div class="d-inline-flex align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $article->rating ? 'text-warning' : 'text-muted' }} fa-sm"></i>
                                                @endfor
                                                <small class="ml-1 text-muted">({{ $article->rating_count }})</small>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <a href="{{ route('knowledge.show', $article) }}" class="btn btn-outline-primary btn-sm">
                                            Ler Artigo
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Paginação -->
                    @if($articles->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $articles->links() }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum artigo encontrado</h4>
                            <p class="text-muted">Esta categoria ainda não possui artigos publicados.</p>
                            @auth
                                <a href="{{ route('admin.knowledge.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-2"></i>Criar Primeiro Artigo
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
