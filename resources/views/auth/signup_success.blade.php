@extends('partials.layout_min')

@push('title')
VideoPace | {{ __('signup.register_success') }}
@endpush

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <div>
                <img src="/images/app/logo-dashboard.png">
            </div>
            <h3>Account Registered</h3>
            <p>Your account is created successfully. An email is sent to your mailbox. Please check and activate your account.</p>
            <a href="{{url('login')}}"><button type="submit" class="btn btn-primary block full-width m-b">Login</button></a>
            <p class="m-t"> <small>© 2017 iVideoExchange. All Rights Reserved.</small> </p>
        </div>
    </div>
@stop
