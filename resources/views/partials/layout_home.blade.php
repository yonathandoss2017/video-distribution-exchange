@extends('partials.layout_min')

@section('body')
  <div id="wrapper-dashboard">
  <div class="header">
	<div class="main-menu desktop">
		<div class="top-menu">
		    <div class="logo-navi">
		    <span class="helper"></span><a href="{{ route('manage.property.select') }}"><img src="/images/app/logo-dashboard.png" alt="home"/></a>
		    </div>

		@include('partials.property_dropdown')

		<div class="user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
	        <ul class="dropdown-menu">
            	<li><a href="{{ url('manage/profile') }}">{{ __('manage/organization/common.profile') }}</a></li>
                <li><a href="{{ url('logout') }}">{{ __('manage/organization/common.logout') }}</a></li>
            </ul>
		 </div>

		<div class="user locale"><a href="{{ route('ivx.setLocale','zh') }}"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></div>
		<div class="user locale"><a href="{{ route('ivx.setLocale','en') }}"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a></div>
		<div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">{{ __('manage/organization/layout.marketplace') }}</a></div>
		@can('super-admin')
			<div class="float-right"><a href="/admin" class="btn btn-normal get-start">{{ __('manage/organization/layout.super_admin') }}</a></div>
		@endcan

		</div>
		<div class="navi">
		<ul class="nav">
		@can('organization-admin')
		<li class="dropdown main-nav-menu">
			<a class="dropdown-toggle @if (!isset($active_menu)) {{ (Request::is('*/playlists')||Request::is('*/playlists/*'))?'active':''||(Request::is('*/organization/videos') || Request::is('*/organization/videos/*'))?'active':''||(Request::is('*/organization/request-logs') || Request::is('*/organization/request-logs/*'))?'active':''||(Request::is('*/start*'))?'active':'' }} @endif" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('common.contents') }} @if(!empty($reviewPlaylistCount))<span class="label label-license">{{ $reviewPlaylistCount }}</span>@endif<i class="fa fa-caret-down" aria-hidden="true"></i></a>
			<ul class="dropdown-menu">
				<li><a class="{{ (Request::is('*/playlists')||Request::is('*/playlists/*'))?'active':'' }}" href="{{ route('manage.organization.playlists.index') }}">{{ __('common.playlists') }}</a></li>
				<li><a class="{{ Request::is('*/organization/videos')?'active':(Request::is('*/organization/videos/*')?'active':'') }}" href="{{ route('manage.organization.videos.index') }}">{{ __('common.videos') }}</a></li>
				<li><a class="{{ Request::is('*/organization/request-logs')?'active':(Request::is('*/organization/request-logs/*')?'active':'') }}" href="{{ route('manage.organization.request-logs.index') }}">{{ __('manage/organization/layout.request_logs') }} @if(!empty($reviewPlaylistCount))<span class="label label-license">{{ $reviewPlaylistCount }}</span>@endif</a></li>
			</ul>
		</li>
		@endcan
		<li><a href="{{ route('manage.property.select') }}" class="{{ (Request::is('manage')||Request::is('manage/property/*'))?'active':'' }}">{{ __('manage/organization/layout.properties') }}</a></li>
		@can('organization-admin')
		<li><a href="{{ route('manage.users') }}" class="{{ (Request::is('*/users')||Request::is('*/users/*'))?'active':'' }}">{{ __('manage/organization/layout.users') }}</a></li>
        <li><a href="{{ route('manage.settings') }}" class="{{ Request::is('*/settings')?'active':'' }}">{{ __('manage/organization/layout.settings') }}</a></li>
		@endcan
		</ul>
		</div>
	</div>

	<div class="main-menu mobile">
			<nav class="navbar navbar-light navbar-toggleable-md menu">
				<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#containerNavbar" aria-controls="containerNavbar" aria-expanded="false" aria-label="Toggle navigation">
				  <span class="navbar-toggler-icon"></span>
				</button>
				<div class="service-platform active"><a href="{{ route('manage.property.select') }}">{{ $orgName }}</a></div>
				<div class="collapse navbar-collapse nav-menu" id="containerNavbar">
				  <ul class="navbar-nav mr-auto">
					<li class="nav-item">
					  <a class="nav-link" href="{{ route('manage.property.select') }}">{{ __('manage/organization/layout.properties') }}</a>
					</li>
					<li class="nav-item">
					  <a class="nav-link" href="{{ route('manage.users') }}">{{ __('manage/organization/layout.users') }}</a>
					</li>
					<li class="nav-item">
					  <a class="nav-link" href="{{ route('manage.settings') }}">{{ __('manage/organization/layout.settings') }}</a>
					</li>
					<li class="user dropdown"><a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
						  <ul class="dropdown-menu">
							<li><a href="{{ url('manage/profile') }}">{{ __('manage/organization/common.profile') }}</a></li>
							<li><a href="{{ url('logout') }}">{{ __('manage/organization/common.logout') }}</a></li>
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
