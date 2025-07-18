@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Editar Ativo: {{ $asset->name }}
                    </h3>
                    <div>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Informações Básicas -->
                            <div class="col-md-6">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle"></i> Informações Básicas
                                </h5>

                                <div class="form-group">
                                    <label for="name">Nome do Ativo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $asset->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="asset_tag">Tag do Ativo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('asset_tag') is-invalid @enderror" 
                                           id="asset_tag" name="asset_tag" value="{{ old('asset_tag', $asset->asset_tag) }}" required>
                                    <small class="form-text text-muted">Identificador único do ativo</small>
                                    @error('asset_tag')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="serial_number">Número de Série</label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                           id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
                                    @error('serial_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="model_id">Modelo <span class="text-danger">*</span></label>
                                    <select class="form-control @error('model_id') is-invalid @enderror" id="model_id" name="model_id" required>
                                        <option value="">Selecione um modelo</option>
                                        @foreach($assetModels as $model)
                                            <option value="{{ $model->id }}" {{ old('model_id', $asset->model_id) == $model->id ? 'selected' : '' }}>
                                                {{ $model->name }} - {{ $model->manufacturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('model_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status_id">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_id') is-invalid @enderror" id="status_id" name="status_id" required>
                                        <option value="">Selecione um status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" {{ old('status_id', $asset->status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Localização e Atribuição -->
                            <div class="col-md-6">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt"></i> Localização e Atribuição
                                </h5>

                                <div class="form-group">
                                    <label for="location_id">Localização</label>
                                    <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
                                        <option value="">Selecione uma localização</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="department_id">Departamento</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                        <option value="">Selecione um departamento</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $asset->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="assigned_to">Atribuído a</label>
                                    <select class="form-control @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                        <option value="">Selecione um usuário</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $asset->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Informações Financeiras -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                    <i class="fas fa-dollar-sign"></i> Informações Financeiras
                                </h5>

                                <div class="form-group">
                                    <label for="purchase_date">Data de Compra</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                           id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="purchase_cost">Custo de Compra</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                                               id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}">
                                        @error('purchase_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="warranty_expires">Garantia Expira</label>
                                    <input type="date" class="form-control @error('warranty_expires') is-invalid @enderror" 
                                           id="warranty_expires" name="warranty_expires" value="{{ old('warranty_expires', $asset->warranty_expires?->format('Y-m-d')) }}">
                                    @error('warranty_expires')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-sticky-note"></i> Observações
                                </h5>
                                <div class="form-group">
                                    <label for="notes">Notas e Observações</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $asset->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Ativo
                        </button>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
