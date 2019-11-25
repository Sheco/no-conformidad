@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ action("DocumentosController@ver", $documento->id) }}" class="btn btn-primary"><span class="oi oi-arrow-thick-left"></span> Regresar</a>
    
    <table class="table table-sm table-bordered table-hover shadow bg-white" style="margin-top: 1em">
        <thead class="thead-dark">
            <tr>
                <th>Fecha</th>
                <th width="100%">Mensaje</th>
            </tr>
        </thead>
        @foreach ($logs as $log)
            <tr>
                <td class="text-nowrap">{{ $log->fecha }}</td>
                <td>{{ $log->mensaje }}</td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
