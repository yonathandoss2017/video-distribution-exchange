@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('common.title') }}
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
                        <div class="col-md-6"><div class="title">{{ __('common.title') }}</div></div>
                        <div class="col-md-6">
                            <div class="right-panel">
                                <a href="{{ route('manage.cp.dpp.create', $property_id) }}" class="btn btn-normal btn-m m-r">{{ __('manage/cp/ivideoadd/ivideoadd.new_dpp') }}</a>
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('manage/cp/ivideoadd/ivideoadd.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('manage.cp.dpp.index', $property_id) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_all') }}</a></li>
                                        <li><a href="{{ route('manage.cp.dpp.index', [$property_id, 'status'=>\App\Models\Playlist::DPP_STATUS_PUBLISHED]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_published') }}</a></li>
                                        <li><a href="{{ route('manage.cp.dpp.index', [$property_id, 'status'=>\App\Models\Playlist::DPP_STATUS_REVIEW]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_review') }}</a></li>
                                        <li><a href="{{ route('manage.cp.dpp.index', [$property_id, 'status'=>\App\Models\Playlist::DPP_STATUS_PROCESSING]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_processing') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="video-list spwp">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="image"></th>
                                    <th class="video-name fx-video-name-dpp">{{ __('manage/cp/ivideoadd/ivideoadd.playlist') }}</th>
                                    <th>{{ __('manage/cp/ivideoadd/ivideoadd.scenes') }}</th>
                                    <th>{{ __('manage/cp/ivideoadd/ivideoadd.submitted_at') }}</th>
                                    <th>{{ __('manage/cp/ivideoadd/ivideoadd.last_updated') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th class="right">{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $status = [
                                        \App\Models\Playlist::DPP_STATUS_PROCESSING => 'label-grey',
                                        \App\Models\Playlist::DPP_STATUS_REVIEW => 'label-orange',
                                        \App\Models\Playlist::DPP_STATUS_PUBLISHED => 'label-active',
                                    ];
                                @endphp
                                @foreach( $playlists as $playlist)
                                    <tr>
                                        <td class="image">
                                            <div class="video-img">
                                                <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $playlist->property_id, null, 240) }}">
                                            </div>
                                        </td>
                                        <td class="video-name fx-video-name-dpp">
                                            <a href="#">{{ $playlist->name }}</a>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $playlist->dpp_entries->sum('scenes_count') }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd.available') }}</small>
                                        </td>
                                        <td>{{ $playlist->dpp_created_at->toFormattedDateString() }}<br>
                                            <small class="timestamp" id="{{ $playlist->id }}_c" dt="{{ $playlist->dpp_created_at->timestamp }}"></small>
                                        </td>
                                        <td>{{ $playlist->dpp_updated_at->toFormattedDateString() }}<br>
                                            <small class="timestamp" id="{{ $playlist->id }}" dt="{{ $playlist->dpp_created_at->timestamp }}"></small>
                                        </td>
                                        <td><span class="label {{ $status[$playlist->dpp_status] }}">{{ __('manage/cp/ivideoadd/ivideoadd.'.$playlist->dppStatusDisplay()) }}</span></td>
                                        <td class="playlist-actions">
                                            <a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist->id]) }}" class="btn btn-normal btn-m">
                                                {{ $playlist->dpp_status == \App\Models\Playlist::DPP_STATUS_PUBLISHED ? __('common.view') : __('common.manage') }}
                                            </a>
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
                                                    'manage/cp/ivideoadd/ivideoadd.showing_from_to_playlists',
                                                    [
                                                        'from'=>$playlists->firstItem(),
                                                        'to'=>$playlists->lastItem(),
                                                        'total'=>$playlists->total()
                                                    ]
                                                )
                                            }}
                                        @else
                                            {{ __('manage/cp/ivideoadd/ivideoadd.no_playlists') }}
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

