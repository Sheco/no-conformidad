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
                        @if ($puedeAsignarResponsable)
                            {{ Form::open([
                                'url'=>"/docs/{$documento->id}/asignarResponsable", 
                                'method'=>'post']) }}
                                {{ Form::select('responsable_usr_id', [''=>'- Seleccionar']+$responsables, $documento->responsable_usr_id, ['class'=>'form-control', 'onchange'=>'this.form.submit()']) }}
                                <span class="status"></span>
                            {{ Form::close() }}
                        @else
                            <div>@if ($documento->responsable_usr_id)
                                    {{ $documento->responsable->name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        @endif
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
                <div class="card-header{{ $propuesta->headerStyle }}">Propuesta {{ $propuesta->id }}</div>
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
        @if (Gate::allows('agregarPropuesta', $documento))
        <div class="col-md-6" style="margin-top: 1em">
        {{ Form::open([
            'url'=>"/docs/$documento->id/agregarPropuesta", 
            'method'=>'post'
            ]) }}
            <div class="card">
                <div class="card-header">Nueva Propuesta</div>
                <div class="card-body container">
                    <div class="row formaTabular">
                        <div class="col-md-12">
                            <label>Comentarios:</label>
                            {{ Form::textarea('descripcion', '', ['class'=>'form-control']) }}
                        </div>
                        <div class="col-md-12 text-right" style="margin-top: 1em">
                            {{ Form::submit('Enviar', ['class'=>'btn btn-primary']) }}
                        </div>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
        </div>
        @endif
    </div>
</div>
@endsection
