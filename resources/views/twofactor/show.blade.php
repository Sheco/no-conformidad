@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Autenticación de dos pasos</div>

                <div class="card-body">
                    <p>
                        La autenticación de dos pasos esta activa para esta cuenta.
                        ¿Quieres desactivarla?
                    </p>

                    {{ Form::open([
                        'url'=>action('TwoFactorController@disable'),
                        'method'=>'delete',
                        ]) }}
                        @csrf
                        <input type="submit" class="btn btn-danger" value="Desactivar">
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
