@extends('partials.layout_min')

@push('title')
    VideoPace | {{ __('signup.login') }}
@endpush

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <div>
               <img src="/images/app/logo-dashboard.png">
            </div>
            <h3>{{ __('signup.account_login') }}</h3>
            @if (session()->exists('well_done'))
                <div class="errors p-a-30">
                    <div class="alert alert-success" role="alert">
                        <strong>{{ trans('signup.well_done') }}</strong> {{ trans('signup.successful_activate') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('invalid_token'))
                <div class="errors p-a-30">
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ __('signup.oops') }}</strong> {{ trans('signup.invalid_token') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('no_user_right'))
                <div class="errors p-a-30">
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ __('signup.oops') }}</strong> {{ trans('signup.no_user_right') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('error'))
                <div class="errors p-a-30">
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ __('signup.oops') }}</strong> {{ trans('signup.error') }}
                    </div>
                </div>
            @endif
            @if (session()->exists('logout'))
                <div class="errors p-a-30">
                    <div class="alert alert-success" role="alert">
                        {!! Session::pull('logout') !!}
                    </div>
                </div>
            @endif
            @if(!session()->exists('invalid_token') && ($errors->has('email') || $errors->has('inactive') || $errors->has('disable')))
                <div class="alert alert-danger">
                    @if($errors->has('email'))
                        <p> <strong>{{ __('signup.oops') }}</strong> {{ $errors->first('email') }}</p>
                    @endif
                    @if ($errors->has('inactive'))
                        <a href="{{url('activate_password/email/'.$errors->first('inactive'))}}">{{ trans('auth.inactive') }}</a>
                    @endif
                </div>
            @endif

            {{ Form::open(['method'=>'POST', 'url'=>url('login'), 'class'=>'m-t']) }}

                <div class="form-group{{ !session()->exists('invalid_token') && $errors->has('email') ? " has-danger" : "" }}">
                    <input type="email" name="email" class="form-control{{ !session()->exists('invalid_token') && $errors->has('email') ? " form-control-danger" : "" }}" placeholder="{{ __('signup.email') }}" required="" value="{{ old('email') }}">
                </div>

                <div class="form-group{{ $errors->has('password') ? " has-danger" : "" }}">
                    <input type="password" name="password" class="form-control{{ $errors->has('email') ? " form-control-danger" : "" }}" placeholder="{{ __('signup.password') }}" required="">
                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                </div>

                <div class="form-check">
                    @if(config('app.name') == 'cnc')
                        {{--<div class="form-check-label"><label><input type="checkbox" class="form-check-input"><i></i> {{ __('signup.user') }}</label></div>--}}
                    @else
                        <div class="form-check-label"><label><input name="remember" type="checkbox" class="form-check-input"><i></i> {{ __('signup.remember_me') }}</label></div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary block full-width m-b">{{ __('signup.login') }}</button>
            {{ Form::close() }}

                <a href="{{ url('/password/reset') }}"><small>{{ __('signup.forgotten_password') }}?</small></a>
                <!--<p class="text-muted text-center"><small>Don't have an account?</small></p>
                <a class="btn btn-normal btn-m register" href="{{ url('signup') }}">Create an account</a>-->

            <p class="m-t"> <small>{{ __('app.copy_right', ['year' => date('Y')]) }}</small> </p>
        </div>
    </div>
@stop
