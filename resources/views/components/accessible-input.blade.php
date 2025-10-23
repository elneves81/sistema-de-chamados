{{--
    Componente de Input Acessível
    
    Uso:
    @include('components.accessible-input', [
        'name' => 'email',
        'label' => 'E-mail',
        'type' => 'email',
        'required' => true,
        'helpText' => 'Digite seu e-mail corporativo'
    ])
--}}

@php
    $type = $type ?? 'text';
    $required = $required ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $placeholder = $placeholder ?? '';
    $value = $value ?? old($name);
    $error = $error ?? $errors->first($name);
    $helpText = $helpText ?? null;
    $id = $id ?? $name;
    $class = $class ?? '';
    $autocomplete = $autocomplete ?? null;
    $maxlength = $maxlength ?? null;
    $minlength = $minlength ?? null;
    $pattern = $pattern ?? null;
    
    $inputClasses = [
        'form-control',
        'input-touch-friendly',
        $error ? 'is-invalid' : '',
        $class
    ];
    
    $labelClasses = [
        'form-label',
        $required ? 'form-label-required' : ''
    ];
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="{{ implode(' ', array_filter($labelClasses)) }}">
        {{ $label }}
        @if($required)
            <span class="text-danger" aria-label="obrigatório">*</span>
        @endif
    </label>
    
    <input 
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        class="{{ implode(' ', array_filter($inputClasses)) }}"
        value="{{ $value }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required aria-required="true" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        @if($minlength) minlength="{{ $minlength }}" @endif
        @if($pattern) pattern="{{ $pattern }}" @endif
        @if($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        @if($helpText && !$error) aria-describedby="{{ $id }}-help" @endif
        {{ $attributes }}
    >
    
    @if($helpText && !$error)
        <small id="{{ $id }}-help" class="form-text">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
            {{ $helpText }}
        </small>
    @endif
    
    @if($error)
        <div id="{{ $id }}-error" class="invalid-feedback" role="alert">
            <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
            {{ $error }}
        </div>
    @endif
</div>
