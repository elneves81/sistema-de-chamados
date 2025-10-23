{{--
    Componente de Modal Acessível
    
    Uso:
    @include('components.accessible-modal', [
        'id' => 'myModal',
        'title' => 'Título do Modal',
        'size' => 'lg',
        'content' => 'Conteúdo do modal'
    ])
--}}

@php
    $id = $id ?? 'modal-' . uniqid();
    $title = $title ?? 'Modal';
    $size = $size ?? 'md';
    $scrollable = $scrollable ?? false;
    $centered = $centered ?? true;
    $closeButton = $closeButton ?? true;
    $footer = $footer ?? null;
    $class = $class ?? '';
    
    $modalClasses = [
        'modal',
        'fade',
        $class
    ];
    
    $dialogClasses = [
        'modal-dialog',
        'modal-dialog-responsive',
        $size !== 'md' ? 'modal-' . $size : '',
        $scrollable ? 'modal-dialog-scrollable' : '',
        $centered ? 'modal-dialog-centered' : ''
    ];
@endphp

<div 
    id="{{ $id }}"
    class="{{ implode(' ', array_filter($modalClasses)) }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    aria-modal="true"
>
    <div class="modal-backdrop" data-modal-close></div>
    
    <div class="{{ implode(' ', array_filter($dialogClasses)) }}" role="document">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}-title">
                    {{ $title }}
                </h5>
                
                @if($closeButton)
                    <button 
                        type="button" 
                        class="modal-close" 
                        data-modal-close
                        aria-label="Fechar modal"
                    >
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                @endif
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                @if(isset($content))
                    {{ $content }}
                @else
                    {{ $slot }}
                @endif
            </div>
            
            <!-- Footer -->
            @if($footer || isset($footerSlot))
                <div class="modal-footer">
                    @if(isset($footerSlot))
                        {{ $footerSlot }}
                    @else
                        {{ $footer }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
