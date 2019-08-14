@extends('partials.layout_min')

@push('title')
VideoPace | Register
@endpush

@section('body')
<div class="middle-box text-center loginscreen">
    <div>
        <div>
            <img src="/images/app/logo-dashboard.png">
        </div>
        <h3>{{ trans('signup.register_account') }}</h3>
        @if (session('errors'))
			<div class="alert alert-danger">
			<strong>{{ trans('signup.oops') }}</strong> {{ trans('signup.error') }}
			</div>
        @endif
        <form class="m-t" role="form" method="POST" action="{{ url('/signup') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" name="organization_name" value="{{ old('organization_name') }}" class="form-control" placeholder="{{ trans('signup.organization_name') }}" required="">
            </div>
            <div class="form-group">
                <input type="text" name="organization_address" value="{{ old('organization_address') }}" class="form-control" placeholder="{{ trans('signup.organization_address') }}" required="">
            </div>
            <div class="form-group">
                {!! Form::select('organization_country',trans('country'),null,['placeholder' => trans('signup.organization_country'),'class' => 'form-control','required' => '']) !!}
            </div>
            <div class="form-group">
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="{{ trans('common.name') }}" required="">
            </div>
            @if ($errors->has('email'))
                <div class="form-group has-danger">
                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-danger" placeholder="{{ trans('signup.email_address') }}" required="">
                <p>{{ $errors->first('email') }}</p>
                </div>
            @else
                <div class="form-group">
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="{{ trans('signup.email_address') }}" required="">
                </div>
            @endif
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="{{ trans('signup.password') }}" required="">
            </div>
            <label class="custom-control custom-checkbox">
                <input id="agree" name="agree" type="checkbox" class="custom-control-input">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">{{ trans('signup.i_agree_to_the_terms_and_conditions') }}</span>
            </label>
            <button type="submit" class="btn btn-primary block full-width m-b" disabled="true">{{ trans('signup.register') }}</button>
            <p class="text-muted text-center"><small>{{ trans('signup.already_have_an_account') }}</small></p>
            <a class="btn btn-normal btn-m register" href="{{ url('login') }}">{{ trans('signup.login') }}</a>
        </form>
        <p class="m-t"> <small>© 2017 iVideoExchange. All Rights Reserved.</small> </p>
    </div>
</div>
@stop

@push('js')
<script>
    $("#agree").on("click", function () {
        if ($(".full-width").prop("disabled") === true) {
            $(".full-width").prop("disabled", false);
        } else {
            $(".full-width").prop("disabled", true);
        }
    });
</script>
@endpush
