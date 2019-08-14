@extends('partials.layout_min')

@push('header_scripts')
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
@endpush

@section('body')
<div id="wrapper-dashboard">
    <div class="header">
    <div class="main-menu desktop">
        <div class="top-menu">
            <div class="logo-navi">
                <span class="helper"></span><a href="{{ route('manage.property.select') }}"><img src="/images/app/logo-dashboard.png" alt="home"/></a>
            </div>
        </div>
    </div>
    </div>

    <!-- Begin page content -->
    @yield('content')

    <footer class="footer">
      <div class="container">
        <center>{{ __('app.copy_right', ['year' => date('Y')]) }}</center>
      </div>
    </footer>
</div>
@stop
