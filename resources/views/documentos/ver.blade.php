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
                        <div>{{ $documento->created_at->format('Y/M/d') }}</div>
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
                    <div class="col-md-4 form-group">
                        <label>Tiempo estimado para la siguiente etapa</label>
                        <div>{{ $documento->fechaMaximaDiffForHumans }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Fecha maxima de entrega</label>
                        <div>{{ $documento->fecha_maxima }}</div>
                    </div>
                    <div class="col-md-4">
                        <label>Acciones</label>
                        <div>
                            <a href="{{ action("DocumentosController@logs", $documento->id) }}">Ver log</a>
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Descripción</label>
                        <div>{{ $documento->descripcion }}</div>
                    </div>
                </div>
                <div class="row" style="margin-top: 1em">
                    <div class="col-md-4">
                        @if (Gate::allows('corregir', $documento)) 
                            {{ Form::open([
                                'url' => "/docs/$documento->id/corregir",
                                'method'=>'post'
                                ]) }}
                                {{ Form::submit('Marcar como corregido', ['class'=>'btn btn-info']) }}
                            {{ Form::close() }}
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if (Gate::allows('verificar', $documento)) 
                            {{ Form::open([
                                'url' => "/docs/$documento->id/verificar",
                                'method'=>'post'
                                ]) }}
                                {{ Form::submit('Marcar como verificado', 
                                    ['class'=>'btn btn-success']) }}
                            {{ Form::close() }}
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if (Gate::allows('cerrar', $documento)) 
                            {{ Form::open([
                                'url' => "/docs/$documento->id/cerrar",
                                'method'=>'post'
                                ]) }}
                                {{ Form::submit('Cerrar', 
                                    ['class'=>'btn btn-secondary']) }}
                            {{ Form::close() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($documento->propuestas as $propuesta)
        <div class="col-md-6" style="margin-top: 1em">
            <div class="card">
                <div class="card-header{{ $propuesta->headerStyle }}">Propuesta {{ $loop->iteration }}</div>
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
                        <div class="col-md-6">
                            <label>Fecha propuesta de entrega</label>
                            <div>{{ $propuesta->fecha_entrega }}</div>
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
                    <div class="row" style="margin-top: 1em">
                    @if ($propuesta->id == $ultimaPropuesta and 
                        Gate::allows('rechazarPropuesta', $documento))
                        <div class="offset-md-6 col-md-3 text-right">
                        {{ Form::open([
                            'url'=>"/docs/$propuesta->id/rechazarPropuesta",
                            'method'=>'post'
                            ]) }}
                            {{ Form::submit('Rechazar', ['class'=>'btn btn-danger']) }}
                        {{ Form::close() }}
                        </div>
                    @endif
                    @if ($propuesta->id == $ultimaPropuesta and 
                        Gate::allows('aceptarPropuesta', $documento))
                        <div class="col-md-3 text-right">
                        {{ Form::open([
                            'url'=>"/docs/$propuesta->id/aceptarPropuesta",
                            'method'=>'post',
                            ]) }}
                            {{ Form::submit('Aceptar', ['class'=>'btn btn-primary']) }}
                        {{ Form::close() }}
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
                            <label>Fecha de entrega</label>
                            {{ Form::date('fecha_entrega', '', ['class'=>'form-control', 'max'=>$fechaMaximaEntrega->format('Y-m-d'), 'min'=>date('Y-m-d', strtotime("tomorrow"))]) }}
                        </div>
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
