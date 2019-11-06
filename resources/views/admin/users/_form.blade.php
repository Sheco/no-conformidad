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

@if ($user->id)
<div class="row" style="margin-top: 1em">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Departamentos suscritos</div>
            <div class="card-body">
                {{ Form::open(['url'=>action('Admin\UsersController@addDepartamento', [$user->id]), 'method'=>'post']) }}
                {{ Form::select('departamento_id', $departamentos->pluck('nombre', 'id'), '') }}
                {{ Form::submit('Agregar', ['class'=>'btn btn-primary']) }}
                
                {{ Form::close() }}
                
                @foreach ($user->departamentos as $departamento)
                    {{ Form::open(['url'=>action('Admin\UsersController@delDepartamento', [$user->id, $departamento->id]), 'method'=>'post']) }}
                    <button><span class="oi oi-x text-danger"></span></button>
                    {{ $departamento->nombre }} 

                    {{ Form::close() }}
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Roles</div>
            <div class="card-body">
                {{ Form::open(['url'=>action('Admin\UsersController@addRole', [$user->id]), 'method'=>'post']) }}
                {{ Form::select('role_id', $roles->pluck('name', 'id'), '') }}
                {{ Form::submit('Agregar', ['class'=>'btn btn-primary']) }}
                
                {{ Form::close() }}
                
                @foreach ($user->roles as $role)
                    {{ Form::open(['url'=>action('Admin\UsersController@delRole', [$user->id, $role->id]), 'method'=>'post']) }}
                    <button><span class="oi oi-x text-danger"></span></button>
                        {{ $role->name }}
                    {{ Form::close() }}
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-md-3 offset-md-9">
        <div class="card" style="margin-top: 1em">
        {{ Form::open(['url'=>action('Admin\UsersController@destroy', $user->id),
            'method'=>'delete']) }}
            <div class="card-header bg-danger text-light">Borrar a este usuario</div>
            <div class="card-body">
                <p>Aquí se puede borrar este usuario, esta operación no puede ser revertida, usese con precaución.</p>

                <button onclick="return confirm('Seguro que desea continuar?')" class="btn btn-danger">Borrar</button>
                
            </div>
        </div>
    </div>
{{ Form::close() }}
</div>


@else
<div class="row" style="margin-top: 1em">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Departamentos suscritos</div>
            <div class="card-body">
                Para asignar departamentos, primero guarde la forma de arriba.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Roles</div>
            <div class="card-body">
                Para asignar roles, primero guarde la forma de arriba.
            </div>
        </div>
</div>
@endif
