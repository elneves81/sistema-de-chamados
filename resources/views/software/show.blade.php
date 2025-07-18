@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalhes do Software</h3>
                    <div class="card-tools">
                        <a href="{{ route('software.edit', $software) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('software.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 200px">Nome</th>
                            <td>{{ $software->name }}</td>
                        </tr>
                        <tr>
                            <th>Versão</th>
                            <td>{{ $software->version }}</td>
                        </tr>
                        <tr>
                            <th>Fabricante</th>
                            <td>{{ $software->manufacturer->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tipo</th>
                            <td>{{ $software->type }}</td>
                        </tr>
                        <tr>
                            <th>Descrição</th>
                            <td>{{ $software->description ?: 'Não disponível' }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Licença</th>
                            <td>{{ $software->is_paid ? 'Pago' : 'Gratuito' }}</td>
                        </tr>
                        @if($software->is_paid)
                            <tr>
                                <th>Chave de Licença</th>
                                <td>{{ $software->license_key ?: 'Não disponível' }}</td>
                            </tr>
                            <tr>
                                <th>Licenças</th>
                                <td>
                                    @if($software->license_count)
                                        {{ $software->used_licenses }}/{{ $software->license_count }} ({{ $software->available_licenses }} disponíveis)
                                    @else
                                        Ilimitado
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Data de Compra</th>
                                <td>{{ $software->purchase_date ? $software->purchase_date->format('d/m/Y') : 'Não disponível' }}</td>
                            </tr>
                            <tr>
                                <th>Data de Expiração</th>
                                <td>{{ $software->expiration_date ? $software->expiration_date->format('d/m/Y') : 'Não disponível' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $status = $software->license_status;
                                    $statusClass = [
                                        'free' => 'success',
                                        'unlimited' => 'info',
                                        'valid' => 'success',
                                        'depleted' => 'danger',
                                        'expired' => 'warning'
                                    ][$status] ?? 'secondary';
                                    $statusText = [
                                        'free' => 'Gratuito',
                                        'unlimited' => 'Ilimitado',
                                        'valid' => 'Válido',
                                        'depleted' => 'Sem licenças',
                                        'expired' => 'Expirado'
                                    ][$status] ?? 'Desconhecido';
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Cadastrado em</th>
                            <td>{{ $software->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Atualizado em</th>
                            <td>{{ $software->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dispositivos com este Software</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assignAssetModal">
                            <i class="fas fa-plus"></i> Associar Dispositivo
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($software->assets->count() > 0)
                                    @foreach($software->assets as $asset)
                                        <tr>
                                            <td>{{ $asset->name }}</td>
                                            <td>{{ $asset->assetType->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $asset->assetStatus->color ?? 'secondary' }}">
                                                    {{ $asset->assetStatus->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('software.unassign-asset', [$software, $asset->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja desassociar este dispositivo?')">
                                                        <i class="fas fa-unlink"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum dispositivo associado</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para associar dispositivos -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAssetModalLabel">Associar Dispositivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('software.assign-asset', $software) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Selecione os dispositivos</label>
                        <select class="form-control select2" name="asset_ids[]" multiple required>
                            @foreach(\App\Models\Asset::whereDoesntHave('software', function($query) use ($software) {
                                $query->where('software_id', $software->id);
                            })->orderBy('name')->get() as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} - {{ $asset->assetType->name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Associar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Selecione os dispositivos',
            allowClear: true
        });
    });
</script>
@endsection
