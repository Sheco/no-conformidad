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
            <div style="margin-bottom: 1em">
                    <a href="{{ action("DocumentosController@filtros") }}" class="btn btn-secondary"><span class="oi oi-eyedropper"></span> Filtros...</a>
                @foreach ($ui_filtros as $filtro=>$valor)
                    <span style="margin-right: 1em">
                        <strong>{{ $filtro }}</strong>: "{{$valor}}"
                    </span>
                @endforeach
            </div>

            <table class="table table-sm table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th><span class="oi oi-star text-warning" title="Puedes avanzar este documento" aria-hidden="true"></span></th>
                    <th>ID</th>
                    <th>Folio</th>
                    @unless (Arr::has($ui_filtros, 'Creador'))
                    <th>Creador</th>
                    @endunless
                    <th>Título</th>
                    @unless (Arr::has($ui_filtros, 'Departamento'))
                    <th>Departamento</th>
                    @endunless
                    @unless (Arr::has($ui_filtros, 'Tipo'))
                    <th>Tipo</th>
                    @endunless
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Tiempo&nbsp;Límite&nbsp;</th>
                    @if (!$status)
                        <th>Status</th>
                    @endif
                </tr>
                </thead>
                @foreach ($docs as $doc) 
                <tr>
                    <td>@puedeAvanzar($doc)
                        <span class="oi oi-star text-warning" title="Puedes avanzar este documento" aria-hidden="true"></span>

                    @endpuedeAvanzar</td>
                    <td>{{ $doc->id }}</td>
                    <td style="white-space: nowrap"><a href="{{ url(action('DocumentosController@ver', $doc->id))}}">{{ $doc->folio }}</a></td>
                    @unless (Arr::has($ui_filtros, 'Creador'))
                    <td>{{ $doc->creador->name }}</td>
                    @endunless
                    <td>{{ $doc->titulo }}</td>
                    @unless (Arr::has($ui_filtros, 'Departamento'))
                    <td>{{ $doc->departamento->nombre }}</td>
                    @endunless
                    @unless (Arr::has($ui_filtros, 'Tipo'))
                    <td>{{ $doc->tipo->nombre }}</td>
                    @endunless
                    <td>{{ $doc->created_at->format("Y/M/d") }}</td>
                    <td>@if ($doc->responsable_usr_id) 
                            {{ $doc->responsable->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $doc->tiempoLimiteLegible }}</td>
                    @if (!$status)
                        <td>{{ $doc->status->nombre }}</td>
                    @endif
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
