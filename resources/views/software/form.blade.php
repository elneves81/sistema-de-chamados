@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ isset($software) ? 'Editar Software' : 'Cadastrar Software' }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($software) ? route('software.update', $software) : route('software.store') }}" method="POST">
                        @csrf
                        @if(isset($software))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nome *</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        value="{{ old('name', isset($software) ? $software->name : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="version">Versão *</label>
                                    <input type="text" class="form-control" id="version" name="version" required
                                        value="{{ old('version', isset($software) ? $software->version : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturer_id">Fabricante *</label>
                                    <select class="form-control" id="manufacturer_id" name="manufacturer_id" required>
                                        <option value="">Selecione um fabricante</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{ $manufacturer->id }}"
                                                {{ old('manufacturer_id', isset($software) ? $software->manufacturer_id : '') == $manufacturer->id ? 'selected' : '' }}>
                                                {{ $manufacturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipo *</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Selecione um tipo</option>
                                        <option value="Sistema Operacional" {{ old('type', isset($software) ? $software->type : '') == 'Sistema Operacional' ? 'selected' : '' }}>Sistema Operacional</option>
                                        <option value="Utilitário" {{ old('type', isset($software) ? $software->type : '') == 'Utilitário' ? 'selected' : '' }}>Utilitário</option>
                                        <option value="Aplicativo" {{ old('type', isset($software) ? $software->type : '') == 'Aplicativo' ? 'selected' : '' }}>Aplicativo</option>
                                        <option value="Antivírus" {{ old('type', isset($software) ? $software->type : '') == 'Antivírus' ? 'selected' : '' }}>Antivírus</option>
                                        <option value="Desenvolvimento" {{ old('type', isset($software) ? $software->type : '') == 'Desenvolvimento' ? 'selected' : '' }}>Desenvolvimento</option>
                                        <option value="Banco de Dados" {{ old('type', isset($software) ? $software->type : '') == 'Banco de Dados' ? 'selected' : '' }}>Banco de Dados</option>
                                        <option value="Escritório" {{ old('type', isset($software) ? $software->type : '') == 'Escritório' ? 'selected' : '' }}>Escritório</option>
                                        <option value="Gráfico" {{ old('type', isset($software) ? $software->type : '') == 'Gráfico' ? 'selected' : '' }}>Gráfico</option>
                                        <option value="Outro" {{ old('type', isset($software) ? $software->type : '') == 'Outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', isset($software) ? $software->description : '') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_paid" id="is_paid" value="1"
                                            {{ old('is_paid', isset($software) ? $software->is_paid : false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_paid">
                                            Software Pago
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="license_count">Quantidade de Licenças</label>
                                    <input type="number" class="form-control" id="license_count" name="license_count" min="1"
                                        value="{{ old('license_count', isset($software) ? $software->license_count : '') }}">
                                    <small class="form-text text-muted">Deixe em branco para licenças ilimitadas</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="license_key">Chave de Licença</label>
                                    <input type="text" class="form-control" id="license_key" name="license_key"
                                        value="{{ old('license_key', isset($software) ? $software->license_key : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="purchase_date">Data de Compra</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        value="{{ old('purchase_date', isset($software) && $software->purchase_date ? $software->purchase_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiration_date">Data de Expiração</label>
                                    <input type="date" class="form-control" id="expiration_date" name="expiration_date"
                                        value="{{ old('expiration_date', isset($software) && $software->expiration_date ? $software->expiration_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($software) ? 'Atualizar' : 'Cadastrar' }} Software
                            </button>
                            <a href="{{ route('software.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isPaidCheckbox = document.getElementById('is_paid');
        const licenseFields = document.querySelectorAll('#license_count, #license_key, #purchase_date, #expiration_date');
        
        function toggleLicenseFields() {
            const disabled = !isPaidCheckbox.checked;
            licenseFields.forEach(field => {
                field.disabled = disabled;
                if (disabled) {
                    field.value = '';
                }
            });
        }
        
        toggleLicenseFields();
        isPaidCheckbox.addEventListener('change', toggleLicenseFields);
    });
</script>
@endsection
