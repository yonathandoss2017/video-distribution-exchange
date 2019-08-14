@extends('admin.layout')
@push('title') {{ __('admin/sidebar.exchange') }} | {{ __('app.title') }} @endpush
@push('head-scripts')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush
@section('content')
<div class="row">

    <header class="title-header col-md-12">
        <h3 class="title">{{ __('admin/sidebar.exchange') }}</h3>
    </header>

    <div class="col-4">
        <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.exchange') }}</a> <span class="brdcrmb-item">/</span>  <strong class="brdcrmb-item">{{ __('admin/common.home') }}</strong></p>
    </div><!-- .col-* -->

    <div class="col-8 min-menu">
        <div class="right-panel">
            {!! Form::open(['method' => 'GET', 'url' => route('admin.exchange.index'), 'id' => 'exchange_form']) !!}
            <div class="input-group sr">
                <input type="text" placeholder="{{ __('manage/sp/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->get('search') }}">
                {!! Form::hidden('status', request('status')) !!}
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-normal">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </span>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="right-panel">
            <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('manage/sp/exchange/tod.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
                <li><a href="{{ route('admin.exchange.index') }}">{{ __('manage/sp/common.all') }}</a></li>
                <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW.'&search='. request('search') }}">{{ __('manage/sp/exchange/tod.platform_review') }}</a></li>
                <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED.'&search='. request('search') }}">{{ __('manage/sp/exchange/tod.platform_rejected') }}</a></li>
            </ul>
        </div>
    </div><!-- .col-* -->

    <div class="col-md-12 search-result">
        @if($search = request('search'))
            {{ __('manage/sp/common.keywords') }}: <div class="search-result-info"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.request('status') }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>&nbsp;&nbsp;&nbsp;
        @endif
        @if($status)
            {{ __('common.status') }}:
            <div class="search-result-info" style="margin-top: 0px; margin-bottom: 10px;">
                    <span class="search-result-text">
                        @if( $status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                            {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW) }}
                        @elseif($status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED)
                            {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED) }}
                        @endif
                    </span>
                <span class="search-close"><a href="{{ url()->current(). '?search='.request('search') }}"><i class="fa fa-times" aria-hidden="true"></i></a></span>
            </div>
        @endif
    </div>

    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-content playlist-list">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="playlist-title" style="width: 35%;">{{ __('manage/cp/exchange/distribution.terms') }}</th>
                        <th style="width: 120px;">{{ __('common.playlists') }}</th>
                        <th class="date">{{ __('common.created_at') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('admin/common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($termsOfDistributions as $termsOfDistribution)
                    <tr>
                        <td class="playlist-title" style="width: 35%;">
                            <a href="{{ route('admin.exchange.show', $termsOfDistribution->id) }}">
                                @if($termsOfDistribution->name == 'Whitelist for own SP')
                                    {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                                @else
                                    {{ $termsOfDistribution->name }}
                                @endif
                            </a>
                            <br>
                            <small>{{ __('manage/sp/exchange/tod.distribute_from') }}: {{ $termsOfDistribution->contentProvider->organization->name }} » {{ $termsOfDistribution->contentProvider->name }}</small>
                            <br>
                            @if($termsOfDistribution->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                                <small>{{ __('manage/cp/exchange/distribution.distribute_to') }}: {{ __('manage/sp/exchange/tod.whitelist_internal') }}</small>
                            @else
                                <small>{{ __('manage/cp/exchange/distribution.distribute_to') }}: {{ $termsOfDistribution->serviceProvider ? $termsOfDistribution->serviceProvider->organization->name : '-' }} » {{ $termsOfDistribution->serviceProvider ? $termsOfDistribution->serviceProvider->name : '-' }}</small>
                            @endif
                        </td>
                        <td class="playlist-amount">
                            <span class="amount">{{ $termsOfDistribution->getPlaylistCount() }}</span>
                            <small>{{ __('manage/sp/common.playlists') }}</small>
                        </td>
                        <td class="date">
                            <div id="{{ $termsOfDistribution->id }}-{{ $termsOfDistribution->created_at->timestamp }}"></div>
                            <small class="timestamp" id="{{ $termsOfDistribution->id }}" dt="{{ $termsOfDistribution->created_at->timestamp }}"></small>
                        </td>
                        <td>
                            @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                                <span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                            @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED)
                                <span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                            @endif
                        </td>
                        <td class="playlist-actions">
                            <a href="{{ route('admin.exchange.show', $termsOfDistribution->id) }}" class="btn btn-normal btn-m">{{ __('manage/sp/common.view') }}</a>
                            @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                                {{ Form::open(['method' => 'PUT', 'route' => ['admin.exchange.approve', $termsOfDistribution->id] ]) }}
                                <button type="submit" class="btn btn-normal btn-m">{{ __('manage/sp/common.approve') }}</button>
                                {{ Form::close() }}
                                {{ Form::open(['method' => 'PUT', 'route' => ['admin.exchange.reject', $termsOfDistribution->id] ]) }}
                                <button type="submit" class="btn btn-normal btn-m">{{ __('manage/sp/common.reject') }}</button>
                                {{ Form::close() }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-5">
                        <div class="dataTables_info">
                            @if ($termsOfDistributions->count()>0)
                                {{
                                    __(
                                        'manage/sp/exchange/tod.tod_pagination',
                                        [
                                            'from'=>$termsOfDistributions->firstItem(),
                                            'to'=>$termsOfDistributions->lastItem(),
                                            'total'=>$termsOfDistributions->total()
                                        ]
                                    )
                                }}
                            @else
                                {{ __('manage/cp/exchange/distribution.no_terms') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="dataTables_paginate paging_simple_numbers">
                            {{ $termsOfDistributions->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div><!-- .ibox-content -->
        </div><!-- .ibox -->
    </div><!-- .col* -->
</div><!-- .row -->
@stop
@push('foot-scripts')
    <script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush