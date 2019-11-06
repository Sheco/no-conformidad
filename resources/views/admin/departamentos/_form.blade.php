{{ Form::model($departamento, [ 'url'=>$url, 'method'=>$method ]) }}
<div class="card">
    <div class="card-header">{{ $title }}</div>
    <div class="card-body">
        <div class="row formaTabular">
            <div class="col-md-6 form-group">
                <label>Nombre</label>
                {{ Form::text('nombre', null, ['class'=>'form-control']) }}
            </div>
        </div>
        <div class="col-md-12 text-right">
            <a href="{{ action('Admin\DepartamentosController@index') }}" class="btn btn-secondary">Cancelar</a>
            {{ Form::submit('Guardar', ['class'=>'btn btn-primary']) }}
        </div>
    </div>
</div>
{{ Form::close() }}

@if ($departamento->id)
    <div class="col-md-3 offset-md-9">
        <div class="card" style="margin-top: 1em">
        {{ Form::open(['url'=>action('Admin\DepartamentosController@destroy', $departamento->id),
            'method'=>'delete']) }}
            <div class="card-header bg-danger text-light">Borrar a este departamento</div>
            <div class="card-body">
                <p>Aquí se puede borrar este departamento, esta operación no puede ser revertida, usese con precaución.</p>

                <button onclick="return confirm('Seguro que desea continuar?')" class="btn btn-danger">Borrar</button>
                
            </div>
        </div>
    </div>
{{ Form::close() }}
</div>
@endif
