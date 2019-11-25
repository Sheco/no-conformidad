@extends('layouts.app')

@section('content')
  <div class="container">
    @include('admin.departamentos._form', [
        'url'=>action('Admin\DepartamentosController@store'),
        'title'=>'Nuevo departamento',
        'method'=>'post',
        ])
  </div>
@endsection
