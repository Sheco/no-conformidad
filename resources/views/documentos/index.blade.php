@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <ul class="list-group">
                    @foreach ($statuses as $_status)
                        <li class="list-group-item{{ ($_status->codigo == $status? " active": "") }}">
                            <a class="nav-link {{ ($_status->codigo == $status? " text-warning": "") }}" href="{{ url('docs/status', $_status->codigo) }}">
                            {!! $_status->documentosVisiblesBadge($user) !!}
                            {{ $_status->nombre }}
                        </a>
                    </li>
                    @endforeach
            </nav>
        </div>
        <div class="col-md-9">
            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th><span class="oi oi-star text-warning" title="Puedes avanzar este documento" aria-hidden="true"></span></th>
                    <th>ID</th>
                    <th>Folio</th>
                    <th>Creador</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Límite&nbsp;de&nbsp;entrega</th>
                </tr>
                </thead>
                @foreach ($docs as $doc) 
                <tr>
                    <td>@if ($doc->puedeAvanzar(Auth::user())) 
                        <span class="oi oi-star text-warning" title="Puedes avanzar este documento" aria-hidden="true"></span>

                    @endif</td>
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
                    <td>{{ $doc->fechaMaximaDiffForHumans }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
