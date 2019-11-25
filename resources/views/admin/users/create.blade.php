@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.users._form', [
        'url'=>action('Admin\UsersController@store'),
        'title'=>'Nuevo usuario',
        'method'=>'post',
        ])
  <div class="row" style="margin-top: 1em">
      <div class="col-md-6">
          <div class="card shadow">
              <div class="card-header">Departamentos suscritos</div>
              <div class="card-body">
                  Para asignar departamentos, primero guarde la forma de arriba.
              </div>
          </div>
      </div>

      <div class="col-md-6">
          <div class="card shadow">
              <div class="card-header">Roles</div>
              <div class="card-body">
                  Para asignar roles, primero guarde la forma de arriba.
              </div>
          </div>
  </div>
</div>
@endsection
