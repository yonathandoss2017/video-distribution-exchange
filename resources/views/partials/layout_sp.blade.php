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
                <li><a href="{{ url('manage/profile') }}">{{ __('manage/sp/common.profile') }}</a></li>
                <li><a href="{{ url('logout') }}">{{ __('manage/sp/common.logout') }}</a></li>
              </ul>
		 </div>
		<div class="user locale"><a href="{{ route('ivx.setLocale','zh') }}"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></div>
		<div class="user locale"><a href="{{ route('ivx.setLocale','en') }}"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a></div>
        <div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">{{ __('manage/sp/layout.marketplace') }}</a></div>
		@can('super-admin')
			<div class="float-right"><a href="/admin" class="btn btn-normal get-start">{{ __('manage/sp/layout.super_admin') }}</a></div>
		@endcan
		</div>
		<div class="navi">
		<ul class="nav">

		<li class="dropdown main-nav-menu">
            <a class="dropdown-toggle {{ Request::is('*/playlists')||Request::is('*/playlists/*')||Request::is('*/terms/*')||Request::is('*/videos')||Request::is('*/videos/*') ? 'active':'' }}" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/sp/layout.contents') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('manage.sp.playlists.index', $property->id) }}">{{ __('manage/sp/common.playlists') }}</a></li>
				<li><a href="{{ route('manage.sp.videos', $property->id) }}">{{ __('manage/sp/common.videos') }}</a></li>
            </ul>
        </li>

        <li class="dropdown main-nav-menu">
            <span class="feature-label text-blue">{{ __('manage/sp/common.app') }}</span>
            <a class="dropdown-toggle {{ Request::is('*/syndication')||Request::is('*/syndication/*') ?'active':'' }}" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/sp/layout.ivideo_site') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('manage.sp.syndication.create', $property->id) }}">{{ __('manage/sp/layout.syndications') }}</a></li>
            </ul>
        </li>

        <li class="dropdown main-nav-menu">
            <a class="dropdown-toggle {{ Request::is('*/request-log', '*/request-log/*') || Request::is('*/terms-of-distributions', '*/terms-of-distributions/*') ? 'active' : '' }}" data-toggle="dropdown" href="#">
                {{ __('manage/sp/layout.exchange') }}
                @if($tod_sp_pending > 0) 
                    <span class="label label-license">{{ $tod_sp_pending }}</span>
                @endif
                <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu exchange">
                <li>
                    <a href="{{ route('manage.sp.tod.index', $property_id) }}">{{ __('manage/sp/layout.terms_of_distribution') }}
                    @if($tod_sp_pending > 0) 
                        <span class="label label-license">{{ $tod_sp_pending }}</span>
                    @endif
                    </a>
                </li>
                <li><a href="{{ route('manage.sp.request-log.index', $property) }}">{{ __('manage/sp/layout.request_logs') }}</a></li>
            </ul>
        </li>

        <li class="dropdown main-nav-menu" style="display:none;">
            <a class="{{ (Request::is('*/sp/analytics') || Request::is('*/sp/analytics/*') || Request::is('*/revenue') || Request::is('*/revenue/*')) ? 'active' : '' }} dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/sp/layout.reports') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('manage.sp.analytics.index', $property->id) }}" class="{{ Request::is('*/analytics')?'active':'' }}">{{ __('manage/sp/layout.analytics') }}</a></li>
            </ul>
        </li>

        <li class="dropdown main-nav-menu">
            <a href="#" class="dropdown-toggle {{ in_array(true, [Request::is('*/settings/property'), Request::is('*/settings/notifications')]) ? 'active' : '' }}" data-toggle="dropdown">
                {{ __('manage/sp/common.settings') }} <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('manage.sp.settings.property', $property->id) }}">{{ __('manage/sp/layout.property_settings') }}</a>
                </li>
                <li>
                    <a href="{{ route('manage.sp.settings.notifications', $property->id) }}">{{ __('manage/sp/layout.notification_settings') }}</a>
                </li>
            </ul>
        </li>
		</ul>
		</div>
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
        <div class="text-center">{{ __('app.copy_right', ['year' => date('Y')]) }}</div>
      </div>
    </footer>
</div>
@stop
