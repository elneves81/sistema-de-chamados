{{--
    Componente de Botão Acessível
    
    Uso:
    @include('components.accessible-button', [
        'text' => 'Salvar',
        'type' => 'submit',
        'variant' => 'primary',
        'icon' => 'bi-check',
        'ariaLabel' => 'Salvar formulário'
    ])
--}}

@php
    $type = $type ?? 'button';
    $variant = $variant ?? 'primary';
    $size = $size ?? 'md';
    $disabled = $disabled ?? false;
    $fullWidth = $fullWidth ?? false;
    $loading = $loading ?? false;
    $icon = $icon ?? null;
    $iconPosition = $iconPosition ?? 'left';
    $ariaLabel = $ariaLabel ?? $text;
    $id = $id ?? null;
    $class = $class ?? '';
    
    $buttonClasses = [
        'btn',
        'btn-' . $variant,
        'btn-' . $size,
        $fullWidth ? 'w-100' : '',
        $loading ? 'loading' : '',
        $class
    ];
@endphp

<button 
    type="{{ $type }}"
    @if($id) id="{{ $id }}" @endif
    class="{{ implode(' ', array_filter($buttonClasses)) }}"
    @if($disabled || $loading) disabled @endif
    @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
    {{ $attributes }}
>
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        <span class="sr-only">Carregando...</span>
    @endif
    
    @if($icon && $iconPosition === 'left' && !$loading)
        <i class="{{ $icon }} me-2" aria-hidden="true"></i>
    @endif
    
    <span>{{ $text }}</span>
    
    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }} ms-2" aria-hidden="true"></i>
    @endif
</button>
