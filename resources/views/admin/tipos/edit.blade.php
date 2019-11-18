@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.tipos._form', [
        'url'=>action('Admin\TiposController@update', $tipo->id),
        'title'=>"Actualizar tipo $tipo->nombre",
        'method'=>'put',
        ])
</div>
@endsection

