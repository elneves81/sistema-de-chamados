@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Localização: {{ $location->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
        <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-info">
            <i class="bi bi-eye"></i> Visualizar
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
                <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome da Localização *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" id="name" value="{{ old('name', $location->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="short_name" class="form-label">Código/Sigla</label>
                                <input type="text" class="form-control @error('short_name') is-invalid @enderror" 
                                       name="short_name" id="short_name" value="{{ old('short_name', $location->short_name) }}" 
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
                                  placeholder="Rua, número, complemento">{{ old('address', $location->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       name="city" id="city" value="{{ old('city', $location->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="state" class="form-label">Estado/Província</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       name="state" id="state" value="{{ old('state', $location->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="postal_code" class="form-label">CEP/Código Postal</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                       name="postal_code" id="postal_code" value="{{ old('postal_code', $location->postal_code) }}">
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
                            <option value="Brasil" {{ old('country', $location->country) == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Estados Unidos" {{ old('country', $location->country) == 'Estados Unidos' ? 'selected' : '' }}>Estados Unidos</option>
                            <option value="Canadá" {{ old('country', $location->country) == 'Canadá' ? 'selected' : '' }}>Canadá</option>
                            <option value="Argentina" {{ old('country', $location->country) == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                            <option value="Chile" {{ old('country', $location->country) == 'Chile' ? 'selected' : '' }}>Chile</option>
                            <option value="México" {{ old('country', $location->country) == 'México' ? 'selected' : '' }}>México</option>
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
                                       name="phone" id="phone" value="{{ old('phone', $location->phone) }}" 
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
                                       name="email" id="email" value="{{ old('email', $location->email) }}">
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
                                  placeholder="Observações sobre esta localização">{{ old('comment', $location->comment) }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Localização ativa
                            </label>
                        </div>
                        <small class="form-text text-muted">Localizações inativas não aparecem em seleções de formulários.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Atualizar Localização
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Informações</h6>
            </div>
            <div class="card-body">
                <p><strong>Criado em:</strong><br>{{ $location->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Última atualização:</strong><br>{{ $location->updated_at->format('d/m/Y H:i') }}</p>
                
                @if($location->users()->count() > 0)
                    <p><strong>Usuários nesta localização:</strong><br>
                    <span class="badge bg-info">{{ $location->users()->count() }}</span></p>
                @endif
                
                @if($location->assets()->count() > 0)
                    <p><strong>Ativos nesta localização:</strong><br>
                    <span class="badge bg-success">{{ $location->assets()->count() }}</span></p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Ações Perigosas</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" 
                      onsubmit="return confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja EXCLUIR esta localização?\n\nEsta ação não pode ser desfeita!')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-trash"></i> Excluir Localização
                    </button>
                </form>
                <small class="form-text text-muted mt-2">
                    A exclusão só será permitida se não houver usuários ou ativos vinculados.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
