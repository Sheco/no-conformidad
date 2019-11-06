@extends('layouts.app')

@section('content')
<div class="container">
    @include('admin.users._form', [
        'url'=>action('Admin\UsersController@update', $user->id),
        'title'=>"Actualizar usuario $user->name",
        'method'=>'put',
        ])
</div>
@endsection

