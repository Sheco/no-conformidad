@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Crear nuevo documento</div>
                <div class="card-body">
                    <div class="container">
                        @include("documentos._forma", ['url'=>'/docs/crear'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

