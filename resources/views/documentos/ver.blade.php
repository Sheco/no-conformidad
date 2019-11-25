@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow">
        <div class="card-header">Documento {{ $documento->folio }} "{{$documento->titulo}}"</div>
        <div class="card-body">
            <div class="container formaTabular">
                <div class="row">
                    <div class="col-md-4">
                        <label>Fecha de creación</label>
                        <div>{{ $documento->created_at->format('Y/M/d') }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Departamento</label>
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
                        @can('asignarResponsables', $documento)
                            {{ Form::open([
                                'url'=>"/docs/{$documento->id}/asignarResponsable", 
                                'method'=>'post']) }}
                                {{ Form::select('responsable_usr_id', 
                                    $responsables, 
                                    $documento->responsable_usr_id, 
                                    [
                                        'class'=>'form-control', 
                                        'onchange'=>'this.form.submit()',
                                        'placeholder'=>'- Seleccionar',
                                    ]) }}
                                <span class="status"></span>
                            {{ Form::close() }}
                        @else
                            <div>@if ($documento->responsable_usr_id)
                                    {{ $documento->responsable->name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        @endcan
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Tiempo límite para la siguiente etapa</label>
                        <div>{{ $documento->tiempoLimiteLegible }}</div>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Fecha maxima de entrega</label>
                        <div>{{ $documento->limite_maximo->format('Y/M/d') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label>Acciones</label>
                        <div>
                            <a href="{{ action("DocumentosController@logs", $documento->id) }}">Ver log</a>
                        </div>
                    </div>
                    @if ($documento->archivos()->count()) 
                    <div class="col-md-4">
                        <label>Archivos</label>
                        <ul>
                        @foreach ($documento->archivos as $archivo)
                            <li><a href="{{ action("DocumentosController@archivo", $archivo) }}">{{ $archivo->nombre }}</a></li>
                        @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="col-md-12 form-group">
                        <label>Descripción</label>
                        <div>{{ $documento->descripcion }}</div>
                    </div>
                </div>
                <div class="row" style="margin-top: 1em">
                    <div class="col-md-4">
                        @can('corregir', $documento) 
                            {{ Form::open([
                                'url' => "/docs/$documento->id/corregir",
                                'method'=>'post'
                                ]) }}
                                {{ Form::submit('Marcar como corregido', ['class'=>'btn btn-info']) }}
                            {{ Form::close() }}
                        @endcan
                    </div>
                    <div class="col-md-4">
                        @can('verificar', $documento)
                            {{ Form::open([
                                'url' => "/docs/$documento->id/verificar",
                                'method'=>'post'
                                ]) }}
                            {{ Form::submit('Marcar como verificado', 
                                ['class'=>'btn btn-success']) }}
                            {{ Form::close() }}
                        @endcan
                    </div>
                    <div class="col-md-4">
                        @can('cerrar', $documento)
                            {{ Form::open([
                                'url' => "/docs/$documento->id/cerrar",
                                'method'=>'post'
                                ]) }}
                            {{ Form::submit('Cerrar', 
                                ['class'=>'btn btn-secondary']) }}
                            {{ Form::close() }}
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach ($documento->propuestas as $propuesta)
        <div class="col-md-6" style="margin-top: 1em">
            <div class="card shadow">
                <div class="card-header{{ $propuesta->headerStyle }}">Propuesta {{ $loop->iteration }}</div>
                <div class="card-body container">
                    <div class="row formaTabular">
                        <div class="col-md-6">
                            <label>Fecha creación</label>
                            <div>{{ $propuesta->created_at->format('Y/M/d') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label>Responsable:</label>
                            <div>{{ $propuesta->responsable->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label>Fecha propuesta de entrega</label>
                            <div>{{ $propuesta->fecha_entrega->format('Y/M/d') }}</div>
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
                    @if ($loop->last)
                        @can('rechazar', $propuesta)
                        <div class="offset-md-6 col-md-3 text-right">
                        {{ Form::open([
                            'url'=>action('DocumentosController@rechazarPropuesta', $propuesta->id),
                            'method'=>'post'
                            ]) }}
                        {{ Form::submit('Rechazar', ['class'=>'btn btn-danger']) }}
                        {{ Form::close() }}
                        </div>
                        @endcan
                        @can('aceptar', $propuesta)
                        <div class="col-md-3 text-right">
                        {{ Form::open([
                            'url'=>action('DocumentosController@aceptarPropuesta', $propuesta->id),
                            'method'=>'post',
                            ]) }}
                        {{ Form::submit('Aceptar', ['class'=>'btn btn-primary']) }}
                        {{ Form::close() }}
                        </div>
                        @endcan
                    @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @can('agregarPropuestas', $documento)
        <div class="col-md-6" style="margin-top: 1em">
        {{ Form::open([
            'url'=>"/docs/$documento->id/agregarPropuesta", 
            'method'=>'post'
            ]) }}
            <div class="card shadow">
                <div class="card-header">Nueva Propuesta</div>
                <div class="card-body container">
                    <div class="row formaTabular">
                        <div class="col-md-12">
                            <label>Fecha de entrega</label>
                            {{ Form::date('fecha_entrega', '', ['class'=>'form-control', 'max'=>$documento->limiteMaximoPropuesta->format('Y-m-d'), 'min'=>date('Y-m-d', strtotime("tomorrow"))]) }}
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
        @endcan
    </div>
</div>
@endsection
