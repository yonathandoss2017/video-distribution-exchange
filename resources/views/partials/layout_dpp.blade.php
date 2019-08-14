@extends('partials.layout_min')

@section('body')
<div id="wrapper-dashboard">
    <div class="header">
        <div class="main-menu desktop">
            <div class="top-menu">
                <div class="logo-navi">
                    <span class="helper"></span><img src="/images/app/logo-dashboard.png" alt="home"/></a>
                </div>
                <div class="service-p active dropdown">
                    <a href="#" class="dropdown-toggle nav-link">{{ __('dpp.dpp_title') }}</a>
                </div>
                <div class="user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('manage/profile') }}">{{ __('common.profile') }}</a></li>
                        <li><a href="{{ url('logout') }}">{{ __('common.logout') }}</a></li>
                    </ul>
                </div>

                <div class="user locale"><a href="{{ route('ivx.setLocale','zh') }}"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></div>
                <div class="user locale"><a href="{{ route('ivx.setLocale','en') }}"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a></div>
                <div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">Marketplace</a></div>
            </div>
            <div class="navi">
                <ul class="nav">
                    <li><a class="{{ (Request::is('dpp/request/new')||Request::is('dpp/request/new/*'))?'active':'' }}" href="{{ route('dpp.request.new.index') }}">{{ __('dpp.dpp_request_new') }}</a></li>
                    <li><a class="{{ (Request::is('dpp/request/ready')||Request::is('dpp/request/ready/*'))?'active':'' }} " href="{{ route('dpp.request.ready') }}" >{{ __('dpp.dpp_request_ready') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="main-menu mobile content">
            <nav class="navbar navbar-light navbar-toggleable-md menu">
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#containerNavbar" aria-controls="containerNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            <div class="collapse navbar-collapse nav-menu" id="containerNavbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('dpp.request.new.index') }}">{{ __('common.playlists') }}</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="{{ route('dpp.request.ready') }}">{{ __('manage/cp/layout.videos') }}</a>
                    </li>
                    <li class="user dropdown"><a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('manage/profile') }}">{{ __('common.profile') }}</a></li>
                            <li><a href="{{ url('logout') }}">{{ __('common.logout') }}</a></li>
                        </ul>
                    </li>
                  </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 text-center">
                @include('partials.errors', ['err_type' => 'header'])
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
