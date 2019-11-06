@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.users._form', [
        'url'=>action('Admin\UsersController@store'),
        'title'=>'Nuevo usuario',
        'method'=>'post',
        ])
</div>
@endsection
