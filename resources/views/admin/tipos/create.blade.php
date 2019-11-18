@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.tipos._form', [
        'url'=>action('Admin\TiposController@store'),
        'title'=>'Nuevo tipo',
        'method'=>'post',
        ])
</div>
@endsection
