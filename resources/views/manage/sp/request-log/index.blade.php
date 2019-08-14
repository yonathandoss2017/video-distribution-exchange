@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/exchange/request_log.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header">
                    <div class="min-menu row">
                        <div class="col-md-3">
                            <div class="title">{{ __('manage/sp/exchange/request_log.page_title') }}</div>
                        </div>
                        <div class="col-md-9">
                            <form method="GET" accept-charset="UTF-8" id="playlists_form">
                                <div class="right-panel">
                                    <div class="input-group sr">
                                        <input type="text" placeholder="{{ __('manage/sp/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->get('search') }}">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-sm btn-normal">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- /.title-header -->
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="video-list spwp">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="playlist-title pt">{{ __('manage/sp/common.title') }}</th>
                                    <th>{{ __('manage/sp/exchange/request_log.requested_by') }}</th>
                                    <th>{{ __('manage/sp/common.playlists') }}</th>
                                    <th>{{ __('manage/sp/common.date') }}</th>
                                    <th class="text-right">{{ __('manage/sp/common.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($requestLogs as $requestLog)
                                <tr>
                                    <td class="playlist-title ptl">
                                        <a href="{{ route('manage.sp.request-log.show', ['property' => $property_id, 'requestLog' => $requestLog]) }}">{{ $requestLog->subject }}</a>
                                    </td>
                                    <td class="playlist-title">
                                        <a>{{ $requestLog->user->name }}</a> <br/>
                                        <small>{{ $requestLog->user->email }}</small>
                                    </td>
                                    <td class="playlist-amount">
                                        <span class="amount">{{ $requestLog->playlists_count }}</span> <small>{{ __('manage/sp/common.playlists') }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $requestLog->created_at->format('M d, Y') }}</div>
                                        <small class="timestamp">{{ $requestLog->created_at->tz('Asia/Singapore')->format('H:i') }}  GMT +0800</small>
                                    </td>
                                    <td class="playlist-actions">
                                        <a href="{{ route('manage.sp.request-log.show', ['property' => $property_id, 'requestLog' => $requestLog]) }}" class="btn btn-normal">{{ __('manage/sp/common.view') }}</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('manage/sp/exchange/request_log.no_request_logs') }}</td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">{{ __('manage/sp/exchange/request_log.showing_from_to_request_logs', ['from' => $requestLogs->firstItem(), 'to' => $requestLogs->lastItem(), 'total' => $requestLogs->total()]) }}</div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $requestLogs->links() }}
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
@endpush
