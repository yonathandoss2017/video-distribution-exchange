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
                        <div class="col-md-3">
                            <div class="title">{{ __('manage/cp/video-compare/compare.video_compare') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                <div class="status-float">
                                    <a href="{{ route('manage.cp.block-chain.video-compare.create', $property_id) }}" class="btn btn-normal btn-m m-r">{{ __('manage/cp/video-compare/compare.new_video_compare') }}</a>
                                </div>
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('manage/cp/video-compare/compare.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ url()->current() }}">{{ __('common.all') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\VideoCompare::STATUS[1] }}">{{ __('manage/cp/video-compare/compare.processing') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\VideoCompare::STATUS[2] }}">{{ __('manage/cp/video-compare/compare.finished') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\VideoCompare::STATUS[0] }}">{{ __('manage/cp/video-compare/compare.failed') }}</a></li>
                                    </ul>
                                </div>
                                <div class="status-float">
                                    <a href="{{ route('manage.cp.block-chain.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/block-chain/block-chain.back_to_block_chain') }}</a>
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
                                    <th class="right" width="6%">#</th>
                                    <th class="video-name" style="width: 32%;">{{ __('manage/cp/video-compare/compare.name') }}</th>
                                    <th>{{ __('manage/cp/video-compare/compare.match_count') }}</th>
                                    <th>{{ __('manage/cp/video-compare/compare.video_duration') }}</th>
                                    <th class="date">{{ __('manage/cp/video-compare/compare.submitted_at') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th class="right" width="10%">{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($videoCompares as $videoCompare)
                                    <tr>
                                        <td class="right" width="6%">{{ $videoCompare->id }}</td>
                                        <td class="video-name" style="width: 32%;">
                                            <a href="#">{{ $videoCompare->title }}</a>
                                        </td>
                                        <td class="playlist-amount">
                                            @if($videoCompare->status == \App\Models\VideoCompare::STATUS_FINISHED)
                                                <span class="amount">{{ $videoCompare->compare_results_count }}</span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="duration">
                                            @if($videoCompare->status == \App\Models\VideoCompare::STATUS_FINISHED)
                                                {{ $videoCompare->duration_ms / 1000 }}{{ __('manage/cp/video-compare/compare.second') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="date">
                                            <div id="{{ $videoCompare->id }}-{{ $videoCompare->created_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $videoCompare->id }}" dt="{{ $videoCompare->created_at->timestamp }}"></small>
                                        </td>
                                        <td>
                                            @if ($videoCompare->status == \App\Models\VideoCompare::STATUS_FINISHED)
                                                <span class="label label-active">{{ __('manage/cp/video-compare/compare.finished') }}</span>
                                            @elseif ($videoCompare->status == \App\Models\VideoCompare::STATUS_PROCESSING)
                                                <span class="label label-orange">{{ __('manage/cp/video-compare/compare.processing') }}</span>
                                            @else
                                                <span class="label label-error">{{ __('manage/cp/video-compare/compare.failed') }}</span>
                                            @endif
                                        </td>
                                        <td class="playlist-actions">
                                            @if($videoCompare->status == \App\Models\VideoCompare::STATUS_FINISHED)
                                                <a href="{{ route('manage.cp.block-chain.video-compare.show', [$property_id, $videoCompare->id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/video-compare/compare.view') }}</a>
                                            @else
                                                <a href="javascript:void(0);" class="btn btn-normal btn-m disabled">{{ __('manage/cp/video-compare/compare.view') }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($videoCompares->count()>0)
                                            {{
                                                __(
                                                    'manage/cp/video-compare/compare.showing_from_to_video_compare',
                                                    [
                                                        'from'=>$videoCompares->firstItem(),
                                                        'to'=>$videoCompares->lastItem(),
                                                        'total'=>$videoCompares->total()
                                                    ]
                                                )
                                            }}
                                        @else
                                            {{ __('manage/cp/video-compare/compare.no_video_compare') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $videoCompares->appends(request()->input())->links() }}
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