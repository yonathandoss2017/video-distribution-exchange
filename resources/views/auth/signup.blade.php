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
        <h3>{{ __('signup.account_registration') }}</h3>
        <p class="m-t">{{ __('signup.ivideo_marketplace_requires_an_active_account') }}<br><br>{{ __('signup.for_more_information') }} <a href="mailto:support@cnc.com">support@cnc.com.</a></p>

        <p class="text-muted text-center m-t-2"><small>{{ __('signup.already_have_an_account') }}</small></p>
        <a class="btn btn-normal btn-m register" href="{{ url('login') }}">{{ __('signup.login') }}</a>

        <p class="m-t"> <small>{{ __('app.copy_right', ['year' => date('Y')]) }}</small> </p>
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
