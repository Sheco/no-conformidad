@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      @include('admin.departamentos._form', [
          'url'=>action('Admin\DepartamentosController@update', $departamento->id),
          'title'=>"Actualizar departamento $departamento->nombre",
          'method'=>'put',
          ])
    </div>

    <div class="col-md-3 offset-md-9">
      <div class="card" style="margin-top: 1em">
      {{ Form::open(['url'=>action('Admin\DepartamentosController@destroy', $departamento->id),
              'method'=>'delete']) }}
        <div class="card-header bg-danger text-light">Borrar a este departamento</div>
        <div class="card-body">
          <p>Aquí se puede borrar este departamento, esta operación no puede ser revertida, usese con precaución.</p>

          <button onclick="return confirm('Seguro que desea continuar?')" class="btn btn-danger">Borrar</button>
                  
        {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

