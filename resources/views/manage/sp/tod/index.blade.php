@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/exchange/tod.page_title') }} | {{ __('app.title') }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header ">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">{{ __('manage/sp/exchange/tod.page_title') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            {!! Form::open(['method' => 'GET', 'url' => route('manage.sp.tod.index', ['property' => $property_id]), 'id' => 'playlists_form']) !!}
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
                                <li><a href="{{ route('manage.sp.tod.index', ['property' => $property_id]) }}">{{ __('manage/sp/common.all') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=active&search='. request('search') }}">{{ __('manage/sp/exchange/tod.active') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=revoked&search='. request('search') }}">{{ __('manage/sp/exchange/tod.cp_revoked') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=declined&search='. request('search') }}">{{ __('manage/sp/exchange/tod.sp_declined') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=discontinue&search='. request('search') }}">{{ __('manage/sp/exchange/tod.sp_discontinued') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=platform_review&search='. request('search') }}">{{ __('manage/sp/exchange/tod.platform_review') }}</a></li>
                                <li><a href="{{ url()->current() .'?status=platform_rejected&search='. request('search') }}">{{ __('manage/sp/exchange/tod.platform_rejected') }}</a></li>
                            </ul>
                        </div>
                        @if (request('status') != 'pending')
                            <div class="right-panel">
                                <a href="{{ route('manage.sp.tod.index', ['property' => $property_id, 'status' => 'pending']) }}" class="btn btn-normal m-r">{{ __('manage/sp/exchange/tod.pending_requests') }} <span class="label label-license">{{ $spPendingTODCount }}</span></a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-12 search-result">
                        @if($search = request('search'))
                            {{ __('manage/sp/common.keywords') }}: <div class="search-result-info"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.request('status') }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if($status)
                                {{ __('common.status') }}:
                            <div class="search-result-info">
                                <span class="search-result-text">
                                    @if( $status == 'active')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_ACTIVE) }}
                                    @elseif($status == 'revoked')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_CP_REVOKED) }}
                                    @elseif($status == 'declined')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_SP_DECLINED) }}
                                    @elseif($status == 'discontinue')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_SP_DISCONTINUE) }}
                                    @elseif($status == 'pending')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_SP_PENDING) }}
                                    @elseif($status == 'platform_review')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW) }}
                                    @elseif($status == 'platform_rejected')
                                        {{ __('manage/sp/exchange/tod.'.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED) }}
                                    @endif
                                </span>
                                <span class="search-close"><a href="{{ url()->current(). '?search='.request('search') }}"><i class="fa fa-times" aria-hidden="true"></i></a></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="playlist-list video-list spwp">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 45%;" >{{ __('manage/sp/exchange/tod.terms') }}</th>
                                    <th style="width: 116px;" >{{ __('manage/sp/common.playlists') }}</th>
                                    <th class="date">{{ __('manage/sp/common.published_at') }}</th>
                                    <th>{{ __('manage/sp/common.status') }}</th>
                                    <th>{{ __('manage/sp/common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($termsOfDistributions as $termsOfDistribution)
                            <tr>
                                <td class="playlist-title ptl">
                                    <a href="{{ route('manage.sp.tod.show', [$property_id, $termsOfDistribution->id]) }}">
                                        @if($termsOfDistribution->name == 'Whitelist for own SP')
                                            {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                                        @else
                                            {{ $termsOfDistribution->name }}
                                        @endif
                                    </a>
                                    <br>
                                    <small>{{ __('manage/sp/exchange/tod.distribute_from') }}: {{ $termsOfDistribution->contentProvider->organization->name }} » {{ $termsOfDistribution->contentProvider->name }}</small>
                                </td>
                                <td class="playlist-amount">
                                    <span class="amount">{{ $termsOfDistribution->getPlaylistCount() }}</span>
                                    <small>{{ __('manage/sp/common.playlists') }}</small>
                                </td>
                                <td>
                                    <div id="{{ $termsOfDistribution->id }}-{{ $termsOfDistribution->published_at->timestamp }}"></div>
                                    <small class="timestamp" id="{{ $termsOfDistribution->id }}" dt="{{ $termsOfDistribution->published_at->timestamp }}"></small>
                                </td>
                                <td>
                                    @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_ACTIVE)
                                    <span class="label label-active">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_SP_PENDING)
                                    <span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_CP_REVOKED)
                                    <span class="label label-revoked">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_SP_DECLINED)
                                    <span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_SP_DISCONTINUE)
                                    <span class="label label-redoutline">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                                    <span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED)
                                    <span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                                    @endif
                                </td>
                                <td class="playlist-actions">
                                    <a href="{{ route('manage.sp.tod.show', [$property_id, $termsOfDistribution->id]) }}" class="btn btn-normal btn-m">{{ __('manage/sp/common.view') }}</a>
                                    @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_SP_PENDING)
                                        {{ Form::open(['method' => 'PUT', 'route' => ['manage.sp.tod.accept', $property_id, $termsOfDistribution->id] ]) }}
                                        <button type="submit" class="btn btn-normal btn-m">{{ __('manage/sp/common.accept') }}</button>
                                        {{ Form::close() }}
                                    @endif

                                    @if($termsOfDistribution->sp_property_id == 0)
                                        <a href="javascript:void(0)" disabled class="btn btn-normal btn-m delete disabled">
                                    @elseif (\App\Models\TermsOfDistribution::STATUS_ACTIVE == $termsOfDistribution->status)
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDiscontinue" data-delete-form-id="{{ $termsOfDistribution->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                                    @elseif (\App\Models\TermsOfDistribution::STATUS_SP_PENDING == $termsOfDistribution->status)
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDecline" data-delete-form-id="{{ $termsOfDistribution->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                                    @else
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDelete" data-delete-form-id="{{ $termsOfDistribution->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                                    @endif
                                        {!! Form::open(['method' => 'DELETE', 'url' => route('manage.sp.tod.delete', $property_id), 'id' => 'form_delete_'. $termsOfDistribution->id]) !!}
                                            {!! Form::hidden('delete_mode', 0) !!}
                                            {!! Form::hidden('tod_id', $termsOfDistribution->id) !!}
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        {!! Form::close() !!}
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('manage/sp/exchange/tod.tod_not_found') }}</td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info">{{ __('manage/sp/exchange/tod.tod_pagination', ['from' => $termsOfDistributions->firstItem(), 'to' => $termsOfDistributions->lastItem(), 'total' => $termsOfDistributions->total()]) }}</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $termsOfDistributions->appends(request()->input())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('manage.sp.tod._modal_discontinue')

@stop

@push('js')
    <script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
