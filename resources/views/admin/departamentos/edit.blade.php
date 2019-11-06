@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.departamentos._form', [
        'url'=>action('Admin\DepartamentosController@update', $departamento->id),
        'title'=>"Actualizar departamento $departamento->nombre",
        'method'=>'put',
        ])
</div>
@endsection

