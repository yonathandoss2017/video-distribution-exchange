@extends('partials.layout_dpp')

@push('title')
    iVideoExchange | iVideoAdd Video
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
                @if ($status == 'processing')
                <div class="title">{{ __('dpp.new_dpp_request') }}</div>
                @else
                <div class="title">{{ __('dpp.ready_dpp_request') }}</div>
                @endif
            </div>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="playlist-list">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="playlist-title pt">{{ __('dpp.Playlist') }}</th>
                                <th style="width: 150px;">{{ __('dpp.Organization') }}</th>
                                <th style="width: 120px;">{{ __('common.videos')}}</th>
                                <th>{{ __('dpp.submitted_at') }}</th>
                                <th>{{ __('common.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach( $playlists as $playlist)
                                <tr>
                                    <td class="playlist-title pt">
                                        <a href="#">{{ $playlist->name }}</a>
                                    </td>
                                    <td>{{ $playlist->content_provider->organization->name }}</td>
                                    <td class="playlist-amount">
                                        <span class="amount">{{ $playlist->dpp_entries_count }}</span> <small>{{ __('common.videos') }}</small>
                                    </td>
                                    <td class="date">
                                        <div id="{{ $playlist->id }}-{{ $playlist->dpp_updated_at->timestamp }}"></div>
                                        <small class="timestamp" id="{{ $playlist->id }}" dt="{{ $playlist->dpp_updated_at->timestamp }}"></small>
                                    </td>
                                    <td class="playlist-actions">
                                        @if ($status == 'processing')
                                        <a href="{{ route('dpp.request.show', [$playlist->id, 'status'=>$status]) }}" class="btn btn-normal btn-m">{{ __('common.manage') }}</a>
                                        @else
                                        <a href="{{ route('dpp.request.show', $playlist->id) }}" class="btn btn-normal btn-m">{{ __('common.manage') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info">
                                    @if ($playlists->count()>0)
                                        {{
                                            __(
                                                'manage/cp/contents/playlists.showing_from_to_playlists',
                                                [
                                                    'from'=>$playlists->firstItem(),
                                                    'to'=>$playlists->lastItem(),
                                                    'total'=>$playlists->total()
                                                ]
                                            )
                                        }}
                                    @else
                                        {{ __('manage/cp/contents/playlists.no_playlists') }}
                                    @endif
                                </div>
                            </div>

                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $playlists->appends(request()->input())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
