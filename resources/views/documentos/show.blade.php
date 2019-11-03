@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Documento {{ $documento->folio }} "{{$documento->titulo}}"</div>
        <div class="card-body">
            <div class="container formaTabular">
                <div class="row">
                    <div class="col-md-4">
                        <label>Fecha de creación</label>
                        <div>{{ $documento->created_at }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Departamento/Buque</label>
                        <div>@if ($documento->departamento_id)
                            {{ $documento->departamento->nombre }}
                        @endif
                        </div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Tipo</label>
                        <div>{{ $documento->tipo->nombre }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Status</label>
                        <div>{!! $documento->status->nombreColoreado !!}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Creador</label>
                        <div>{{ $documento->creador->name }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Responsable</label>
                        <div>@if ($documento->responsable_usr_id)
                            {{ $documento->responsable->name }}
                        @endif
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Descripción</label>
                        <div>{{ $documento->descripcion }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($documento->propuestas as $propuesta)
        <div class="col-md-6" style="margin-top: 1em">
            <div class="card">
                <div class="card-header">Propuesta {{ $propuesta->id }}</div>
                <div class="card-body container">
                    <div class="row formaTabular">
                        <div class="col-md-6">
                            <label>Fecha creación</label>
                            <div>{{ $propuesta->created_at }}</div>
                        </div>
                        <div class="col-md-6">
                            <label>Responsable:</label>
                            <div>{{ $propuesta->responsable->name }}</div>
                        </div>
                        <div class="col-md-12">
                            <label>Comentarios:</label>
                            <div>{{ $propuesta->descripcion }}</div>
                        </div>
                        @if ($propuesta->retro_usr_id)
                        <div class="col-md-6">
                            <label>Retroalimentación</label>
                            <div>{{ $propuesta->retro }}</div>
                        </div>
                        <div class="col-md-6">
                            <label>Retroalimentador</label>
                            <div>{{ $propuesta->retroalimentador->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
