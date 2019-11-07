{{ Form::model($user, [ 'url'=>$url, 'method'=>$method ]) }}
<div class="card">
    <div class="card-header">{{ $title }}</div>
    <div class="card-body">
        <div class="row formaTabular">
            <div class="col-md-6 form-group">
                <label>Email</label>
                {{ Form::email('email', null,  ['class'=>'form-control']) }}
            </div>

            <div class="col-md-6 form-group">
                <label>Nombre</label>
                {{ Form::text('name', null, ['class'=>'form-control']) }}
            </div>

            
            <div class="col-md-6 form-group">
                <label>Serie de documentos</label>
                {{ Form::text('serie_documentos', null, ['class'=>'form-control']) }}
            </div>

            <div class="col-md-6 form-group">
                <label>Folio de documentos</label>
                {{ Form::number('contador_documentos', null, ['class'=>'form-control']) }}
            </div>

            <div class="col-md-6 form-group">
                <label>Departamento asignado</label>
                {{ Form::select('departamento_id', [''=>' - Seleccionar']+$departamentos->pluck('nombre', 'id')->toArray(), null, ['class'=>'form-control']) }}
            </div>

            <div class="col-md-6 form-group">
                <label>Password</label>
                {{ Form::password('password',  ['class'=>'form-control']) }}
            </div>

        </div>
        <div class="col-md-12 text-right">
            <a href="{{ action('Admin\UsersController@index') }}" class="btn btn-secondary">Cancelar</a>
            {{ Form::submit('Guardar', ['class'=>'btn btn-primary']) }}
        </div>
    </div>
</div>
{{ Form::close() }}
