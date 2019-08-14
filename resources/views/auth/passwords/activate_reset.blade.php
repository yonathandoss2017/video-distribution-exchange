@extends('partials.layout_min')

@push('title')
    {{ __('signup.ivideo') }} | {{ __('passwords.set_pwd') }}
@endpush

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <div>
                <img src="/images/app/logo-dashboard.png">
            </div>
            <h3>{{ __('passwords.set_pwd') }}</h3>
            <p>{{ __('passwords.thank_you_for_activating_new_account') }}</p>
            @include('partials.errors', ['err_type' => 'header'])
            {{ Form::open(['method'=>'POST', 'url'=>url('/password/reset'), 'class'=>'m-t']) }}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $decoded_email }}">
                <div class="form-group email">
                    <h3>{{ $decoded_email }}</h3>
                </div>
                <div class="form-group{{ $errors->has('password') ? " has-danger" : "" }}">
                    <input type="password" name="password" class="form-control{{ $errors->has('password') ? " form-control-danger" : "" }}" placeholder="{{ __('passwords.pwd') }}" required="">
                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                </div>
                <div class="form-group{{ $errors->has('password_confirmation') ? " has-danger" : "" }}">
                    <input type="password" name="password_confirmation" class="form-control{{ $errors->has('password_confirmation') ? " form-control-danger" : "" }}" placeholder="{{ __('passwords.confirm_pwd') }}" required="">
                    @include('partials.errors', ['err_type' => 'field','field' => 'password_confirmation'])
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">{{ __('passwords.submit') }}</button>
            {{ Form::close() }}
            <p class="m-t"> <small>{{ __('app.copy_right', ['year' => date('Y')]) }}</small> </p>
        </div>
    </div>
@stop
