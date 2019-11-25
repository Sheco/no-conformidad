@extends('layouts.app')

@section('content')
<div class="container">
  <div style="margin-bottom: 1em">
    <a href="{{ action('Admin\UsersController@index') }}" class="btn btn-primary"><span class="oi oi-arrow-thick-left"></span> Regresar</a>
  </div>
  <table class="table table-sm table-bordered table-hover shadow bg-white">
    <thead class="thead-dark">
      <tr>
        <th>Fecha</th>
        <th>Documento</th>
        <th width="100%">Mensaje</th>
      </tr>
    </thead>
    @foreach ($logs as $log)
      <tr>
        <td class="text-nowrap">{{ $log->fecha }}</td>
        <td class="text-nowarp"><a href="{{ action("DocumentosController@ver", [$log->documento->id]) }}">
            {{ $log->documento->folio }}
          </a></td>
        <td>{{ $log->mensaje }}</td>
      </tr>
    @endforeach
  </table>
</div>
@endsection
