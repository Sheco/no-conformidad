@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group">
                    @foreach ($statuses as $_status)
                        <li class="list-group-item{{ ($_status->codigo == $status? " active": "") }}">
                        <a class="nav-link" href="{{ url('docs/status', $_status->codigo) }}">
                        <span class="badge badge-light">
                            {{ $_status->documentosVisibles($user) }}
                        </span>
                        {!! $_status->nombreColoreado !!}
                    </a>
                    </li>
                    @endforeach
            </nav>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Folio</th>
                    <th>Creador</th>
                    <th>Titulo</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Límite&nbsp;de&nbsp;entrega</th>
                </tr>
                </thead>
                @foreach ($docs as $doc) 
                <tr>
                    <td>{{ $doc->id }}</td>
                    <td><a href="{{ url('/docs/ver', $doc->id)}}">{{ $doc->folio }}</a></td>
                    <td>{{ $doc->creador->name }}</td>
                    <td>{{ $doc->titulo }}</td>
                    <td>{{ $doc->tipo->nombre }}</td>
                    <td>{{ $doc->created_at->format("Y/M/d") }}</td>
                    <td>@if ($doc->responsable_usr_id) 
                            {{ $doc->responsable->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $doc->fechaLimiteDiffforHumans }}</td>
                </tr>
                @endforeach
            </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
