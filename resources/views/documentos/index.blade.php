@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <nav class="nav flex-column">
                    @foreach ($statuses as $_status)
                        <a class="nav-link" href="{{ url('docs/status', $_status->codigo) }}">
                        <span class="badge badge-success text-light">
                            {{ $_status->documentosVisibles($user) }}
                        </span>
                        {!! $_status->nombreColoreado !!}
                        @if ($_status->codigo == $status)
                            *
                        @endif
                    </a>
                    @endforeach
            </nav>
        </div>
        <div class="col-md-9">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Titulo</th>
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Fecha Ultima propuesta</th>
                </tr>
                @foreach ($docs as $doc) 
                <tr>
                    <td><a href="{{ url('/docs/ver', $doc->id)}}">{{ $doc->titulo }}</a></td>
                    <td>{{ $doc->fecha }}</td>
                    <td>@if ($doc->responsable_usr_id) 
                            {{ $doc->responsable->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>@if ($doc->responsable_usr_id && $doc->propuestas->count())
                        {{ $doc->propuestas()->get()->last()->created_at }}
                        @else
                            N/A
                        @endif
                    </td>

                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
