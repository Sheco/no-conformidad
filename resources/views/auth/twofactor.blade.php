@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('/') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="one_time_password" class="col-md-4 col-form-label text-md-right">{{ __('Two factor') }}</label>

                            <div class="col-md-6">
                                <input id="one_time_password" type="text" class="form-control" name="one_time_password" required>

                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Continue') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

