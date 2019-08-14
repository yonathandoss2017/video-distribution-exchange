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

		@include('partials.property_dropdown')

		 <div class="user dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
	          <ul class="dropdown-menu">
                <li><a href="{{ url('manage/profile') }}">{{ __('common.profile') }}</a></li>
                <li><a href="{{ url('logout') }}">{{ __('common.logout') }}</a></li>
              </ul>
		 </div>
		<div class="user locale"><a href="{{ route('ivx.setLocale','zh') }}"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></div>
		<div class="user locale"><a href="{{ route('ivx.setLocale','en') }}"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a></div>
        <div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">{{ __('manage/cp/layout.marketplace') }}</a></div>
		@can('super-admin')
			<div class="float-right"><a href="/admin" class="btn btn-normal get-start">{{ __('manage/cp/layout.super_admin') }}</a></div>
		@endcan
		</div>
        <div class="navi">
            <ul class="nav">
                <li class="dropdown main-nav-menu">
                    <a class="dropdown-toggle @if (!isset($active_menu)) {{ (Request::is('*/playlists')||Request::is('*/playlists/*'))?'active':''||(Request::is('*/cp/videos') || Request::is('*/cp/videos/*'))?'active':''||(Request::is('*/cp/request-logs') || Request::is('*/cp/request-logs/*'))?'active':''||(Request::is('*/start*'))?'active':'' }} @endif" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('common.contents') }} @if($headerDetails['pendingCount'])<span class="label label-license">{{ $headerDetails['pendingCount'] }}</span>@endif<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                    <li><a class="{{ (Request::is('*/playlists')||Request::is('*/playlists/*'))?'active':'' }}" href="{{ route('manage.cp.playlists.index', $property_id) }}">{{ __('common.playlists') }}</a></li>
                    <li><a class="{{ Request::is('*/cp/videos')?'active':(Request::is('*/cp/videos/*')?'active':'') }}" href="{{ route('manage.cp.videos', $property_id) }}">{{ __('common.videos') }}</a></li>
                    @can('manage-cp-request-logs', $property_id)
                        <li><a class="{{ Request::is('*/cp/request-logs')?'active':(Request::is('*/cp/request-logs/*')?'active':'') }}" href="{{ route('manage.cp.request-logs.index', $property_id) }}">{{ __('manage/cp/layout.request_logs') }} @if($headerDetails['pendingCount'])<span class="label label-license">{{ $headerDetails['pendingCount'] }}</span>@endif</a></li>
                    @endcan
                    </ul>
                </li>

                <li class="dropdown main-nav-menu">
                    <a class="dropdown-toggle {{ (Request::is('*/sources')||Request::is('*/sources/*')||Request::is('*/oauths')||Request::is('*/oauths/*'))?'active':'' }}" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('common.sources') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('manage.cp.oauths.index', $property_id) }}">{{ __('manage/cp/layout.oauth_settings') }}</a></li>
                    </ul>
                </li>

                @can('manage-dpp', $property_id)
                <li>
                    <a class="{{ (Request::is('*/dpp')||Request::is('*/dpp/*'))?'active':'' }}" href="{{ route('manage.cp.dpp.index', $property_id) }}">{{ __('manage/cp/layout.dpp') }}</a>
                </li>
                @endcan

                @can('manage-exchange', $property_id)
                <li class="dropdown main-nav-menu">
                    <a class="dropdown-toggle @if (isset($active_menu) && $active_menu == 'exchange') active @else {{ (Request::is('*/cp/exchange/*') && !Request::is('*/notification-settings')) ? 'active' : '' }} @endif" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/cp/layout.exchange') }} @if($headerDetails['notReadRequestLogCount'])<span class="label label-license">{{ $headerDetails['notReadRequestLogCount'] }}</span>@endif<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu exchange">
                        <li><a href="{{ route('manage.cp.exchange.distribution.index', $property_id) }}">{{ __('manage/cp/layout.terms_of_distribution') }}</a></li>
                        <li><a href="{{ route('manage.cp.exchange.marketplace-terms.index', $property_id) }}">{{ __('manage/cp/layout.marketplace_terms') }}</a></li>
                        <li><a href="{{ route('manage.cp.exchange.request_logs.index', $property_id) }}">{{ __('manage/cp/layout.request_logs') }} @if($headerDetails['notReadRequestLogCount'])<span class="label label-license">{{ $headerDetails['notReadRequestLogCount'] }}</span>@endif</a></li>
                    </ul>
                </li>
                @endcan

                <li class="dropdown main-nav-menu" style="display:none;">
                    <a class="{{ (Request::is('*/cp/analytics') || Request::is('*/cp/analytics/*')) ? 'active' : '' }} dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/cp/layout.reports') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('manage.cp.analytics.index', $property_id) }}" class="{{ (Request::is('*/analytics')||Request::is('*/analytics/*'))?'active':'' }}">{{ __('common.analytics') }}</a></li>
                    </ul>
                </li>

                @can('manage-block-chain', $property_id)
                <li class="dropdown main-nav-menu">
                    <a class="{{ (Request::is('*/cp/block-chain') || Request::is('*/cp/block-chain/*')) ? 'active' : '' }} dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('manage/cp/layout.copyright_management') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                      <li><a href="{{ route('manage.cp.block-chain.index', $property_id) }}" class="{{ (Request::is('*/block-chain')||Request::is('*/block-chain/*'))?'active':'' }}">{{ __('manage/cp/layout.block_chain') }}</a></li>
                      <li><a href="#">{{ __('manage/cp/layout.copyright_protection_term') }}</a></li>
                      <li><a href="#">{{ __('manage/cp/layout.copyright_maintenance_entrust_term') }}</a></li>
                    </ul>
                </li>
                @endcan

                @can('manage-setting', $property_id)
                <li class="dropdown main-nav-menu">
                    <a class="dropdown-toggle {{ (Request::is('*/settings') || Request::is('*/exchange/notification-settings'))?'active':'' }}" data-toggle="dropdown" href="#" aria-expanded="true">{{ __('common.settings') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('manage.cp.settings', $property_id) }}">{{ __('manage/cp/layout.property_settings') }}</a></li>
                        <li><a href="{{ route('manage.cp.exchange.notification-settings.index', $property_id) }}">{{ __('manage/cp/layout.notification_settings') }}</a></li>
                    </ul>
                </li>
                @endcan
            </ul>
        </div>
	</div>

	<div class="main-menu mobile content">
			<nav class="navbar navbar-light navbar-toggleable-md menu">
							<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#containerNavbar" aria-controls="containerNavbar" aria-expanded="false" aria-label="Toggle navigation">
							  <span class="navbar-toggler-icon"></span>
							</button>

							@include('partials.property_dropdown')

							<div class="collapse navbar-collapse nav-menu" id="containerNavbar">
							  <ul class="navbar-nav mr-auto">
								<li class="nav-item">
								  <a class="nav-link" href="{{ route('manage.cp.playlists.index', $property_id) }}">{{ __('common.playlists') }}</a>
								</li>
								<li class="nav-item">
								  <a class="nav-link" href="{{ route('manage.cp.videos', $property_id) }}">{{ __('manage/cp/layout.videos') }}</a>
								</li>
								<li class="nav-item">
								  <a class="nav-link" href="{{ route('manage.cp.settings', $property_id) }}">{{ __('common.settings') }}</a>
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
