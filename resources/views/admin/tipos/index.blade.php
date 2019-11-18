@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Tipos</div>
        <div class="card-body">
            <div class="float-right" style="margin-bottom: 1em">
                <a href="{{ action('Admin\TiposController@create') }}" class="btn btn-primary"><span class="oi oi-file"></span> Nuevo tipo</a>
            </div>

            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre</th>
                    </tr>
                </thead>
                @foreach ($tipos as $tipo)
                    <tr>
                        <td><a href="{{ action('Admin\TiposController@edit', [$tipo->id]) }}">
                                <span class="oi oi-pencil"></span>
                                {{ $tipo->nombre }}
                            </a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
