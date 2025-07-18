@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('knowledge.index') }}">Base de Conhecimento</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Resultados da Busca</li>
                </ol>
            </nav>

            <!-- Barra de Pesquisa -->
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <form method="GET" action="{{ route('knowledge.search') }}" class="position-relative">
                        <input type="text" name="q" class="form-control form-control-lg pl-5" 
                               placeholder="Pesquisar artigos..." 
                               value="{{ $query }}">
                        <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>
                        <button type="submit" class="btn btn-primary position-absolute" 
                                style="right: 5px; top: 5px; bottom: 5px;">
                            Buscar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Resultados -->
            @if($query)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-search mr-2"></i>Resultados para "{{ $query }}"
                        <span class="badge badge-primary ml-2">{{ $articles->total() }} encontrados</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($articles->count() > 0)
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
                                        <span class="badge badge-info mr-2">{{ $article->category->name }}</span>
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
                        
                        <!-- Paginação -->
                        @if($articles->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $articles->appends(['q' => $query])->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum resultado encontrado</h4>
                            <p class="text-muted">Tente usar palavras-chave diferentes ou menos específicas.</p>
                            
                            <div class="mt-4">
                                <h6 class="text-muted">Dicas de busca:</h6>
                                <ul class="list-unstyled text-muted small">
                                    <li>• Use palavras-chave simples</li>
                                    <li>• Tente sinônimos</li>
                                    <li>• Verifique a ortografia</li>
                                    <li>• Use menos palavras na busca</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Digite algo para buscar</h4>
                    <p class="text-muted">Use a barra de pesquisa acima para encontrar artigos.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
