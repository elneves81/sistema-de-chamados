@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nova Localização</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('locations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Localização</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('locations.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome da Localização *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" id="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="short_name" class="form-label">Código/Sigla</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror" 
                                       name="short_name" id="short_name" value="{{ old('short_name') }}" 
                                       placeholder="Ex: SP-MTZ">
                                @error('short_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  name="address" id="address" rows="2" 
                                  placeholder="Rua, número, complemento">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       name="city" id="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="state" class="form-label">Estado/Província</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       name="state" id="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">CEP/Código Postal</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       name="postal_code" id="postal_code" value="{{ old('postal_code') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label">País</label>
                        <select class="form-select @error('country') is-invalid @enderror" name="country" id="country">
                            <option value="">Selecione um país</option>
                            <option value="Brasil" {{ old('country') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Estados Unidos" {{ old('country') == 'Estados Unidos' ? 'selected' : '' }}>Estados Unidos</option>
                            <option value="Canadá" {{ old('country') == 'Canadá' ? 'selected' : '' }}>Canadá</option>
                            <option value="Argentina" {{ old('country') == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                            <option value="Chile" {{ old('country') == 'Chile' ? 'selected' : '' }}>Chile</option>
                            <option value="México" {{ old('country') == 'México' ? 'selected' : '' }}>México</option>
                        </select>
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" id="phone" value="{{ old('phone') }}" 
                                       placeholder="Ex: (11) 3000-0000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" id="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comentários</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  name="comment" id="comment" rows="3" 
                                  placeholder="Observações sobre esta localização">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Localização ativa
                            </label>
                        </div>
                        <small class="form-text text-muted">Localizações inativas não aparecem em seleções de formulários.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('locations.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Salvar Localização
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Ajuda</h6>
            </div>
            <div class="card-body">
                <h6><i class="bi bi-info-circle text-primary"></i> Dicas</h6>
                <ul class="small mb-0">
                    <li><strong>Nome:</strong> Use um nome descritivo e único</li>
                    <li><strong>Código:</strong> Sigla para identificação rápida</li>
                    <li><strong>Endereço:</strong> Endereço completo da localização</li>
                    <li><strong>Contato:</strong> Informações para contato local</li>
                </ul>
                
                <hr>
                
                <h6><i class="bi bi-lightbulb text-warning"></i> Exemplos</h6>
                <div class="small">
                    <strong>Matriz:</strong><br>
                    Nome: Matriz - São Paulo<br>
                    Código: SP-MTZ<br>
                    <br>
                    <strong>Filial:</strong><br>
                    Nome: Filial Rio de Janeiro<br>
                    Código: RJ-FIL
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
