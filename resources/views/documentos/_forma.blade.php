{{ Form::model($documento, ['url'=>$url, 'method'=>'post']) }}
@csrf
<div class="row formaTabular">
    <div class="col-md-6">
        <label>Creador</label>
        <div>{{ Auth::user()->name }}</div>
    </div>
    <div class="col-md-6">
        {{ Form::label('tipo_id', 'Tipo') }}
        {{ Form::select('tipo_id', $tipos, null, ['class'=>'form-control']) }}
    </div>
    <div class="col-md-6">
        {{ Form::label('departamento_id', 'Departamento') }}
        {{ Form::select('departamento_id', $departamentos, null, ['class'=>'form-control'])}}
    </div>
    <div class="col-md-6">
        {{ Form::label('titulo', 'Título') }}
        {{ Form::text('titulo', null, ['class'=>'form-control']) }}
    </div>
    <div class="col-md-12">
        {{ Form:: label('descripcion', 'Descripción') }}
        {{ Form::textarea('descripcion', null, ['class'=>'form-control']) }}
    </div>
</div>
<div class="text-right" style="margin-top: 1em">
    {{ Form::submit('Guardar', ['class'=>'btn btn-primary']) }}
</div>
{{ Form::close() }}
