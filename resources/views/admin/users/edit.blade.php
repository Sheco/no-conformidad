@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.users._form', [
        'url'=>action('Admin\UsersController@update', $user->id),
        'title'=>"Actualizar usuario $user->name",
        'method'=>'put',
        ])
  <div class="row" style="margin-top: 1em">
      <div class="col-md-6">
          <div class="card shadow">
              <div class="card-header">Departamentos</div>
              <div class="card-body">
                  {{ Form::open(['url'=>action('Admin\UsersController@addDepartamento', [$user->id]), 'method'=>'post', 'style'=>'margin-bottom: 1em']) }}
                  {{ Form::select('departamento_id', 
                        $departamentosDisponibles
                            ->pluck('nombre', 'id')->toArray(), '', 
                    [
                        'class'=>'form-control', 
                        'onchange'=>'this.form.submit()',
                        'placeholder'=>'- Asignar un departamento',
                    ]) }}
                  
                  {{ Form::close() }}
                  
                  @foreach ($user->departamentos as $departamento)
                      {{ Form::open(['url'=>action('Admin\UsersController@delDepartamento', [$user->id, $departamento->id]), 'method'=>'post']) }}
                      <button><span class="oi oi-x text-danger"></span></button>
                      {{ $departamento->nombre }} 

                      {{ Form::close() }}
                  @endforeach
                  <small><ul>
                          <li>El usuario podra ver documentos que pertenezcan a cualquiera de estos departamentos.</li>
                          <li>Los directores podran asignar responsables a un documento si tienen el departamento del documento</li>

                  </small>
              </div>
          </div>
      </div>

      <div class="col-md-6">
          <div class="card shadow">
              <div class="card-header">Roles</div>
              <div class="card-body">
                  {{ Form::open(['url'=>action('Admin\UsersController@addRole', [$user->id]), 'method'=>'post', 'style'=>'margin-bottom: 1em']) }}
                  {{ Form::select('role_id', $roles
                    ->pluck('name', 'id')->toArray(), '', 
                    [
                        'class'=>'form-control', 
                        'onchange'=>'this.form.submit()',
                        'placeholder'=>'- Asignar un rol'
                    ]) }}
                  
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
          <div class="card shadow" style="margin-top: 1em">
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
</div>
@endsection

