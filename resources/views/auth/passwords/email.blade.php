@extends('partials.layout_min')

@push('title')
    {{ __('signup.ivideo') }} | {{ __('signup.forgotten_password') }}
@endpush

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <div>
                <img src="/images/app/logo-dashboard.png">
            </div>
            @if(session()->has('status'))
                <div class="alert alert-success">
                    <strong>{{ trans('signup.success') }}</strong> {{ trans('signup.get_email') }}
                </div>
            @endif
            <h3>{{ __('signup.forgotten_password') }}?</h3>
            <p>{{ __('passwords.enter_your_email') }}</p>
            {{ Form::open(['method'=>'POST', 'url'=>url('/password/email'), 'class'=>'m-t']) }}
                <div class="form-group{{ $errors->has('email') ? " has-danger" : "" }}">
                    <input type="email" name="email" class="form-control{{ $errors->has('email') ? " form-control-danger" : "" }}" placeholder="{{ __('passwords.email') }}" required="">
                    @include('partials.errors', ['err_type' => 'field','field' => 'email'])
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b"> {{ __('passwords.reset_password') }}</button>
            {{ Form::close() }}
            <p class="m-t"> <small>{{ __('app.copy_right', ['year' => date('Y')]) }}</small> </p>
        </div>
    </div>
@stop
