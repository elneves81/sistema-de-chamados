@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Software</h3>
                    <div class="card-tools">
                        <a href="{{ route('software.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Software
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Versão</th>
                                    <th>Fabricante</th>
                                    <th>Tipo</th>
                                    <th>Licenças</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($software as $sw)
                                    <tr>
                                        <td>{{ $sw->name }}</td>
                                        <td>{{ $sw->version }}</td>
                                        <td>{{ $sw->manufacturer->name ?? 'N/A' }}</td>
                                        <td>{{ $sw->type }}</td>
                                        <td>
                                            @if($sw->is_paid)
                                                @if($sw->license_count)
                                                    {{ $sw->used_licenses }}/{{ $sw->license_count }}
                                                @else
                                                    Ilimitado
                                                @endif
                                            @else
                                                Gratuito
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = $sw->license_status;
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
                                        <td>
                                            <a href="{{ route('software.show', $sw) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('software.edit', $sw) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('software.destroy', $sw) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este software?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
