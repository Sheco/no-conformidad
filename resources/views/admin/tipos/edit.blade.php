@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
    @include('admin.tipos._form', [
        'url'=>action('Admin\TiposController@update', $tipo->id),
        'title'=>"Actualizar tipo $tipo->nombre",
        'method'=>'put',
        ])
    </div>

    <div class="col-md-3 offset-md-9">
        <div class="card shadow" style="margin-top: 1em">
        {{ Form::open(['url'=>action('Admin\TiposController@destroy', $tipo->id),
            'method'=>'delete']) }}
            <div class="card-header bg-danger text-light">Borrar a este tipo</div>
            <div class="card-body">
                <p>Aquí se puede borrar este tipo, esta operación no puede ser revertida, usese con precaución.</p>

                <button onclick="return confirm('Seguro que desea continuar?')" class="btn btn-danger">Borrar</button>
                
            </div>
        {{ Form::close() }}
        </div>
    </div>
  </div>
</div>
@endsection

