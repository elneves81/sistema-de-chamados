@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Editar Licença: {{ $license->software->name ?? 'N/A' }}
                    </h3>
                    <div>
                        <a href="{{ route('licenses.show', $license) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('licenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form action="{{ route('licenses.update', $license) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Informações do Software -->
                            <div class="col-md-6">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-cube"></i> Informações do Software
                                </h5>

                                <div class="form-group">
                                    <label for="software_id">Software <span class="text-danger">*</span></label>
                                    <select class="form-control @error('software_id') is-invalid @enderror" 
                                            id="software_id" name="software_id" required>
                                        <option value="">Selecione um software</option>
                                        @foreach($software as $sw)
                                            <option value="{{ $sw->id }}" {{ old('software_id', $license->software_id) == $sw->id ? 'selected' : '' }}>
                                                {{ $sw->name }} - {{ $sw->publisher }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('software_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="version">Versão</label>
                                    <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                           id="version" name="version" value="{{ old('version', $license->version) }}">
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="license_key">Chave da Licença</label>
                                    <input type="text" class="form-control @error('license_key') is-invalid @enderror" 
                                           id="license_key" name="license_key" value="{{ old('license_key', $license->license_key) }}">
                                    <small class="form-text text-muted">Mantenha confidencial</small>
                                    @error('license_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="type">Tipo de Licença <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Selecione o tipo</option>
                                        <option value="perpetual" {{ old('type', $license->type) == 'perpetual' ? 'selected' : '' }}>
                                            Perpétua
                                        </option>
                                        <option value="subscription" {{ old('type', $license->type) == 'subscription' ? 'selected' : '' }}>
                                            Assinatura
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="seats">Número de Licenças <span class="text-danger">*</span></label>
                                    <input type="number" min="1" class="form-control @error('seats') is-invalid @enderror" 
                                           id="seats" name="seats" value="{{ old('seats', $license->seats) }}" required>
                                    <small class="form-text text-muted">
                                        Quantas instalações são permitidas 
                                        (Atualmente em uso: {{ $license->installations->count() }})
                                    </small>
                                    @error('seats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Informações Comerciais -->
                            <div class="col-md-6">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-dollar-sign"></i> Informações Comerciais
                                </h5>

                                <div class="form-group">
                                    <label for="cost">Custo da Licença</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" 
                                               id="cost" name="cost" value="{{ old('cost', $license->cost) }}">
                                        @error('cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="purchase_date">Data de Compra</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                           id="purchase_date" name="purchase_date" 
                                           value="{{ old('purchase_date', $license->purchase_date?->format('Y-m-d')) }}">
                                    @error('purchase_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="expires_at">Data de Expiração</label>
                                    <input type="date" class="form-control @error('expires_at') is-invalid @enderror" 
                                           id="expires_at" name="expires_at" 
                                           value="{{ old('expires_at', $license->expires_at?->format('Y-m-d')) }}">
                                    <small class="form-text text-muted">Deixe em branco para licenças perpétuas</small>
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="vendor">Fornecedor</label>
                                    <input type="text" class="form-control @error('vendor') is-invalid @enderror" 
                                           id="vendor" name="vendor" value="{{ old('vendor', $license->vendor) }}">
                                    @error('vendor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="support_expires_at">Suporte Expira em</label>
                                    <input type="date" class="form-control @error('support_expires_at') is-invalid @enderror" 
                                           id="support_expires_at" name="support_expires_at" 
                                           value="{{ old('support_expires_at', $license->support_expires_at?->format('Y-m-d')) }}">
                                    @error('support_expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                    <i class="fas fa-toggle-on"></i> Status
                                </h5>

                                <div class="form-group">
                                    <label for="status">Status da Licença <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $license->status) == 'active' ? 'selected' : '' }}>
                                            Ativa
                                        </option>
                                        <option value="inactive" {{ old('status', $license->status) == 'inactive' ? 'selected' : '' }}>
                                            Inativa
                                        </option>
                                        <option value="expired" {{ old('status', $license->status) == 'expired' ? 'selected' : '' }}>
                                            Expirada
                                        </option>
                                    </select>
                                    @error('status')
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
                                              id="notes" name="notes" rows="3">{{ old('notes', $license->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Licença
                        </button>
                        <a href="{{ route('licenses.show', $license) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('licenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Controlar campo de expiração baseado no tipo
    $('#type').on('change', function() {
        if ($(this).val() === 'perpetual') {
            $('#expires_at').prop('disabled', true);
            $('#expires_at').closest('.form-group').find('small').text('Licenças perpétuas não expiram');
        } else {
            $('#expires_at').prop('disabled', false);
            $('#expires_at').closest('.form-group').find('small').text('Deixe em branco para licenças perpétuas');
        }
    });

    // Trigger inicial
    $('#type').trigger('change');
});
</script>
@endsection
