@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Usuários encontrados no LDAP</h2>
    <form method="POST" action="{{ route('ldap.import') }}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Selecionar</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><input type="checkbox" name="users[]" value="{{ $user['email'] }}" checked></td>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['email'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Importar Selecionados</button>
    </form>
</div>
@endsection
