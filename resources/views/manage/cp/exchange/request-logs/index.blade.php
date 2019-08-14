@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/request_logs.request_logs') }}
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
            <div class="title-header">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">{{ __('manage/cp/exchange/request_logs.request_logs') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            {!! Form::open(['route'=>['manage.cp.exchange.request_logs.index', $property_id],'method'=>'GET','class' => 'playlist-search']) !!}
                            <div class="input-group sr">
                                <input type="text" placeholder="{{ __('common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-md-12 search-result">
                        @if($search)
                            {{ __('common.keywords') }} <div class="search-result-info"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current() }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>&nbsp;&nbsp;&nbsp;
                        @endif
                    </div>
                </div>
            </div> <!-- /.title-header -->
            <div class="ibox">
                <div class="ibox-content">
                    <div class="video-list spwp">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="playlist-title pt">{{ __('common.title') }}</th>
                                    <th>{{ __('manage/cp/exchange/request_logs.requested_by') }}</th>
                                    <th>{{ __('common.playlists') }}</th>
                                    <th>{{ __('common.date') }}</th>
                                    <th class="text-right">{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requestLogs as $requestLog)
                                    <tr>
                                        <td class="playlist-title ptl">
                                            <a href="{{ route('manage.cp.exchange.request_logs.show', [$property_id, $requestLog->id]) }}">{{ $requestLog->subject }}</a>
                                        </td>
                                        <td class="playlist-title">
                                            <a>{{ $requestLog->user->name }}</a> <br/>
                                            <small>{{ $requestLog->user->email }}</small>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $requestLog->playlists_count }}</span> <small>{{ __('manage/cp/exchange/request_logs.playlists') }}</small>
                                        </td>
                                        <td>
                                            <div id="{{ $requestLog->id }}-{{ $requestLog->created_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $requestLog->id }}" dt="{{ $requestLog->created_at->timestamp }}"></small>
                                        </td>
                                        <td class="playlist-actions">
                                            <a href="{{ route('manage.cp.exchange.request_logs.show', [$property_id, $requestLog->id]) }}" class="btn btn-normal">{{ __('common.view') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($requestLogs->count()>0)
                                        {{
                                            __(
                                                'manage/cp/exchange/request_logs.showing_from_to_logs',
                                                [
                                                    'from'=>$requestLogs->firstItem(),
                                                    'to'=>$requestLogs->lastItem(),
                                                    'total'=>$requestLogs->total()
                                                ]
                                            )
                                        }}
                                        @else
                                            {{ __('manage/cp/exchange/request_logs.no_logs') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $requestLogs->appends(request()->input())->links() }}
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->
        </div>
    </div>
</div>
@stop

@push('js')
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush