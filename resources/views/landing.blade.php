@extends('partials.layout_min')

@push('title')
VideoPace
@endpush

@section('body')

@push('body_attr')id="landing-background"@endpush

 <div id="wrapper" class="landing-background marketplace">
     <div class="container">
         <div class="main-header ">
             <nav class="navbar navbar-light navbar-toggleable-md menu">
                 <div class="right-menu home side-right">
                     <a href="{{ url('login') }}" class="btn btn-normal btn-m">{{ __('loading.sign_in') }}</a>
                 </div>
                 <div class="user dropdown local">
                     @include('partials.marketplace.right_nav_language')
                 </div>
             </nav>
         </div>
         <div class="landing-content">
             <div class="logo-landing"><img src="/images/app/logo-landing-page.png"></div>
             <div class="welcome-title">
                 <h1>{{ __('loading.title') }}</h1>
                 <p>{!! __('loading.subtitle') !!}</p>
                 <div class="button-to-marketplace">
                     <a class="btn btn btn-primary" href="{{ url('marketplace') }}">{{ __('loading.browse_videos') }}</a>
                 </div>
                 <p class="cp-signup"><a href="{{ url('signup') }}">{{ __('loading.sign_up_now') }}</a></p>
             </div>
         </div>
     </div>
 </div>
@stop
