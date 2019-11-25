@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header">Departamentos</div>
        <div class="card-body">
            <div class="float-right" style="margin-bottom: 1em">
                <a href="{{ action('Admin\DepartamentosController@create') }}" class="btn btn-primary"><span class="oi oi-file"></span> Nuevo departamento</a>
            </div>

            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Usuarios</th>
                    </tr>
                </thead>
                @foreach ($departamentos as $departamento)
                    <tr>
                        <td><a href="{{ action('Admin\DepartamentosController@edit', [$departamento->id]) }}">
                                <span class="oi oi-pencil"></span>
                                {{ $departamento->nombre }}
                            </a></td>
                            <td><a href="{{ action("Admin\UsersController@index") }}?departamento={{ $departamento->id }}">Ver {{ $departamento->users->count() }}</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
