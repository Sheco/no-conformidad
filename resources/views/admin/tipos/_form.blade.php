{{ Form::model($tipo, [ 'url'=>$url, 'method'=>$method ]) }}
<div class="card shadow">
    <div class="card-header">{{ $title }}</div>
    <div class="card-body">
        <div class="row formaTabular">
            <div class="col-md-6 form-group">
                <label>Nombre</label>
                {{ Form::text('nombre', null, ['class'=>'form-control']) }}
            </div>
        </div>
        <div class="col-md-12 text-right">
            <a href="{{ action('Admin\TiposController@index') }}" class="btn btn-secondary">Cancelar</a>
            {{ Form::submit('Guardar', ['class'=>'btn btn-primary']) }}
        </div>
    </div>
</div>
{{ Form::close() }}
