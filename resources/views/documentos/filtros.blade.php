@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Filtros de documentos</div>

                <div class="card-body container">
                    {{ Form::open([
                        "url"=>action("DocumentosController@filtrosGuardar")
                    ]) }}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Departamento</label>
                            {{ Form::select('departamento_id', $departamentos,
                                Arr::get($filtros, "departamento_id"), 
                                ["class"=>"form-control",
                                 "placeholder"=>"- Cualquiera"]) }}
                        </div>

                        <div class="col-md-6">
                            <label>Tipo</label>
                            {{ Form::select('tipo_id', $tipos,
                                Arr::get($filtros, "tipo_id"),
                                ["class"=>"form-control",
                                 "placeholder"=>"- Cualquiera"]) }}
                        </div>
                        @if ($authUser->hasRole('admin') or 
                             $authUser->hasRole('director')) 
                        <div class="col-md-6">
                            <label>Creador</label>
                            {{ Form::select("creador_usr_id", $usuarios, 
                                Arr::get($filtros, "creador_usr_id"), 
                                [ "class"=>"form-control",
                                  "placeholder"=>"- Cualquiera" ]) }}
                        </div>
                        @endif
                    </div>

                    <div class="text-right">
                        <a href="{{ url("/docs") }}" class="btn btn-secondary">Cancelar</a>
                        {{ Form::submit('Aplicar', ['class'=>'btn btn-primary']) }}
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
