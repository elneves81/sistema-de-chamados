{{--
    Componente de Alerta Acessível
    
    Uso:
    @include('components.accessible-alert', [
        'type' => 'success',
        'title' => 'Sucesso!',
        'message' => 'Operação realizada com sucesso',
        'dismissible' => true,
        'autoDismiss' => 5000
    ])
--}}

@php
    $type = $type ?? 'info';
    $title = $title ?? null;
    $message = $message ?? '';
    $dismissible = $dismissible ?? true;
    $autoDismiss = $autoDismiss ?? null;
    $icon = $icon ?? null;
    $class = $class ?? '';
    
    // Ícones padrão por tipo
    $defaultIcons = [
        'success' => 'bi-check-circle-fill',
        'danger' => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        'info' => 'bi-info-circle-fill'
    ];
    
    $alertIcon = $icon ?? ($defaultIcons[$type] ?? 'bi-info-circle-fill');
    
    $alertClasses = [
        'alert',
        'alert-' . $type,
        $dismissible ? 'alert-dismissible' : '',
        $class
    ];
    
    // ARIA role baseado no tipo
    $ariaRole = in_array($type, ['danger', 'warning']) ? 'alert' : 'status';
@endphp

<div 
    class="{{ implode(' ', array_filter($alertClasses)) }}"
    role="{{ $ariaRole }}"
    @if($autoDismiss) data-auto-dismiss="{{ $autoDismiss }}" @endif
    aria-live="polite"
    aria-atomic="true"
>
    <div class="d-flex align-items-start">
        @if($alertIcon)
            <div class="alert-icon">
                <i class="{{ $alertIcon }}" aria-hidden="true"></i>
            </div>
        @endif
        
        <div class="alert-content flex-grow-1">
            @if($title)
                <div class="alert-title">{{ $title }}</div>
            @endif
            
            @if(is_array($message))
                <ul class="mb-0">
                    @foreach($message as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            @else
                <div>{{ $message }}</div>
            @endif
            
            {{ $slot }}
        </div>
        
        @if($dismissible)
            <button 
                type="button" 
                class="alert-close" 
                aria-label="Fechar alerta"
            >
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        @endif
    </div>
</div>
