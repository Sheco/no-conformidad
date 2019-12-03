@extends('layouts.app')

@section('content')
{{ Form::open([
    'url'=>action('TwoFactorController@enable'), 
    'method'=>'post',
    ]) }}
<div class="container">
    <div class="card">
        <div class="card-header">Activar autenticación de dos pasos</div>
        <div class="card-body row">
            <div class="col-md-3">
                <img src="{{ $qrcode }}">
            </div>
            <div class="col-md-4">
                <label>Llave</label>
                <div>{{ $key }}</div>

                <label>Ingrese el código para verificación</label>
                <input type="text" class="form-control" name="code">
                <br>

                <input type="hidden" name="key" value="{{ $key }}">
                <input type="submit" class="btn btn-primary" value="Activar">
            </div>
        </div>
        </div>
    </div>
</div>
{{ Form::close() }}
@endsection
