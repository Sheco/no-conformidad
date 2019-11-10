@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Usuarios</div>
        <div class="card-body">
            <div class="float-right" style="margin-bottom: 1em">
                <a href="{{ action('Admin\UsersController@create') }}" class="btn btn-secondary"><span class="oi oi-file"></span> Nuevo usuario</a>
            </div>

            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Serie</th>
                        <th>Folio</th>
                    </tr>
                </thead>
                @foreach ($users as $user)
                    <tr>
                        <td><a href="{{ action('Admin\UsersController@edit', [$user->id]) }}">
                                <span class="oi oi-pencil"></span>
                                {{ $user->email }}
                            </a></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->serie_documentos }}</td>
                        <td>{{ $user->contador_documentos }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
