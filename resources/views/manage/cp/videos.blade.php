@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('common.videos') }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
    <link href="/css/datapicker/datepicker3.css" rel="stylesheet">
    <script src="/vendor/jquery/jquery.js"></script>
    <script src="/vendor/jquery/jquery-ui.js"></script>
@endpush

@section('content')
@php
    $show_ai_review = \Illuminate\Support\Facades\Gate::allows('view-ai-review', $property_id);
@endphp
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header ">
                    <div class="min-menu row">
                        <div class="col-md-1">
                            <div class="title">{{ __('common.videos') }}</div>
                        </div>
                        <div class="col-md-11">
                            <div class="right-panel">
                                @can('upload-video', $property_id)
                                <div class="status-float">
                                    <a href="{{ route('manage.cp.add.video', $property_id) }}" class="btn btn-normal m-r">{{ __('manage/cp/contents/videos.new_video') }}</a>
                                </div>
                                @endcan

                                <div class="status-float">
                                    <a href="javascript:void(0)" class="btn btn-normal m-r" id="import">{{ __('manage/cp/contents/videos.import') }}</a>
                                    {{ Form::open(['url' => route('manage.cp.video.import.bulk', $property_id), 'id' => 'import-form', 'files' => true]) }}
                                    {{ Form::file('csv_file', ['class' => 'hidden', 'accept' => '.csv', 'id' => 'uploadCsv']) }}
                                    {{ Form::close() }}
                                </div>
                                {!! Form::open(['url'=>route('manage.cp.videos', $property_id),'method'=>'GET', 'id' => 'videos_form', 'style' => 'display:inline-block']) !!}
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('common.bulk_action') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        @if (config('features.content_review'))
                                            <li><a href="javascript:void(0)" onclick="bulk_action(0);">{{ __('manage/cp/contents/videos.request_review') }}</a></li>
                                        @endif
                                        <li><a href="javascript:void(0)" onclick="bulk_action(2);">{{ __('manage/cp/contents/videos.export') }}</a></li>
                                        <li><a href="javascript:void(0)" onclick="bulk_action(1);">{{ __('manage/cp/contents/videos.delete') }}</a></li>
                                    </ul>
                                </div>

                                <div class="status-float m-r">
                                    <div id="reportrange" class="form-control">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span>{{ __('manage/cp/contents/videos.all_time') }}</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </div>
                                </div>

                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('manage/cp/contents/videos.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ url()->current() .'?playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('common.all') }}</a></li>
                                        @if(config('features.content_review'))
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_DRAFT.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.draft') }}</a></li>
                                        @endif
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_READY.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.ready') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_PROCESSING.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.processing') }}</a></li>
                                        @if(config('features.content_review'))
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_PENDING.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.pending') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_REJECTED.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.rejected') }}</a></li>
                                        @endif
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_SCHEDULED.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.scheduled') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_ERROR.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/contents/videos.error') }}</a></li>
                                    </ul>
                                </div>

                                <div class="input-group">
                                    @foreach(request()->except('search', 'sort', 'start_date', 'end_date') as $key=>$value)
                                        {!! Form::hidden($key, $value) !!}
                                    @endforeach
                                    <input type="text" placeholder="{{ __('common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                                </div>
                                <input type="hidden" class="date_sort" name="sort" value="{{ $sort }}">
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-md-12 search-result">
                            @if (isset($feed))
                                {{ __('common.source') }} <div class="search-result-info filter-margin-right"><span class="search-result-text"> {{ $feed->source . ' &raquo; ' . $type[$feed->feedable_type] . ' &raquo; ' . $feed->source_name }}</span><span class="search-close"><a href="{{ url()->current() }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if($playlist)
                                {{ __('common.playlist') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $playlist->name }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if($start_date || $end_date)
                                {{ __('common.date') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $start_date }} - {{ $end_date }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&playlist='.$playlist_id.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if($search)
                                {{ __('common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if(in_array(strtolower($status), $statusArray))

                                {{ __('common.status') }}
                                <div class="search-result-info filter-margin-right">
                                    <span class="search-result-text">{{ ucfirst(__('manage/cp/contents/videos.'. strtolower($status))) }}</span>
                                    <span class="search-close"><a href="{{ url()->current(). '?search='.$search.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="ibox">

                    <div class="ibox-content">
                        <div class="video-list spwp">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="select-all">
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input">
                                                <span class="custom-control-indicator"></span>
                                            </label>
                                        </th>
                                        <th class="image">
                                            <select class='select2' name="" id="select-all">
                                                <option value="0">{{ __('manage/cp/contents/videos.current_page') }}</option>
                                                <option value="1">{{ __('manage/cp/contents/videos.all_page') }}</option>
                                            </select>
                                        </th>
                                        <th class="video-name fx-video-name">{{ __('common.title') }} / {{ __('common.playlist') }}</th>
                                        <th class="duration">{{ __('manage/cp/contents/videos.duration') }}</th>
                                        <th class="duration">{{ __('common.features') }}</th>
                                        <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort">{{ __('common.last_updated') }}</th>
                                        <th class="status">{{ __('common.status') }}</th>
                                        <th class="status text-right">{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($videos as $video)
                                    <tr>
                                        <td>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input video-checkbox" value="{{ $video->id }}">
                                                <span class="custom-control-indicator"></span>
                                            </label>
                                        </td>
                                        <td class="image">
                                            <div class="video-img">
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video->id }}">
                                                    <img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($video, $property_id, null, 120) }}" alt="" onclick="getPlayer('{{ $video -> id }}', '{{ $property_id }}')">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="video-name fx-video-name editable">
                                            <div class="editable-title">
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video->id }}" onclick="getPlayer('{{ $video->id }}', '{{ $property_id }}')">{{ $video->name }}</a>
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="editable-title-input hidden">
                                                <input type="hidden" value="{{ $video->id }}">
                                                <input class="form-control" type="text" value="">
                                                <i class="fa fa-check"></i>
                                                <i class="fa fa-times"></i>
                                            </div>
                                            <div class="playlist-small">
                                                <small>
                                                    <ul>
                                                    @foreach ($video->playlists as $pl)
                                                        <li>{{$pl->name}}</li>
                                                    @endforeach
                                                    </ul>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="duration">
                                            @if ($video->duration >= 3600)
                                                {{gmdate('H:i:s', $video->duration)}}
                                            @elseif ($video->duration > 0)
                                                {{gmdate('i:s', $video->duration)}}
                                            @else
                                                00:00
                                            @endif
                                        </td>
                                        <td class="features">
                                            <!-- ai censorship -->
                                            @if ($show_ai_review)
                                                @if (!empty($video->aiReviewResult) && \App\Models\EntryAiReviewResult::STATUS_SUCCESS == $video->aiReviewResult->ali_status)
                                                    @if (\App\Models\EntryAiReviewResult::SUGGESTION_PASS == $video->aiReviewResult->suggestion) 
                                                        <i class="fa fa-shield text-navy" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.ai_censorship') }}"></i>
                                                    @elseif (\App\Models\EntryAiReviewResult::SUGGESTION_REVIEW == $video->aiReviewResult->suggestion) 
                                                        <i class="fa fa-shield text-orange" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.ai_censorship') }}"></i>
                                                    @elseif (\App\Models\EntryAiReviewResult::SUGGESTION_BLOCK == $video->aiReviewResult->suggestion) 
                                                        <i class="fa fa-shield text-red" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.ai_censorship') }}"></i>
                                                    @endif
                                                @else
                                                    <i class="fa fa-shield text-grey" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.ai_censorship') }}"></i>
                                                @endif
                                            @endif

                                            <!-- block chain -->
                                            @if($video->anzhengEvidence)
                                                <i class="fa fa-copyright text-navy" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.copyright_blockchained') }}"></i>
                                            @else
                                                <i class="fa fa-copyright text-grey" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ __('manage/cp/contents/videos.copyright_unchained') }}"></i>
                                            @endif
                                        </td>
                                        <td class="date">
                                            <div id="{{ $video->id }}-{{ $video->updated_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $video->id }}" dt="{{ $video->updated_at->timestamp }}"></small>
                                        </td>
                                        <td class="status">
                                            @if ($video->status == \App\Models\Entry::STATUS_READY)
                                                @if ($video->isPending)
                                                    <span class="label label-blue">{{ __('manage/cp/contents/videos.scheduled') }}</span>
                                                @else
                                                    <span class="label label-active">{{ __('manage/cp/contents/videos.ready') }}</span>
                                                @endif
                                            @elseif ($video->status == \App\Models\Entry::STATUS_DRAFT)
                                                <span class="label label-grey">{{ __('manage/cp/contents/videos.draft') }}</span>
                                            @elseif ($video->status == \App\Models\Entry::STATUS_PENDING)
                                                <span class="label label-orange">{{ __('manage/cp/contents/videos.pending') }}</span>
                                            @elseif ($video->status == \App\Models\Entry::STATUS_REJECTED)
                                                <span class="label label-rejected">{{ __('manage/cp/contents/videos.rejected') }}</span>
                                            @elseif ($video->status == \App\Models\Entry::STATUS_ERROR)
                                                <span class="label label-error">{{ __('manage/cp/contents/videos.error') }}</span>
                                            @elseif ($video->status == \App\Models\Entry::STATUS_DRAFT)
                                                <span class="label label-error">{{ __('manage/cp/contents/videos.draft') }}</span>
                                            @else
                                                <span class="label label-orange">{{ __('manage/cp/contents/videos.processing') }}</span>
                                            @endif
                                        </td>
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                            <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                  <ul class="dropdown-menu">
                                                    <li class="group">
                                                    <h5>{{ __('manage/cp/contents/videos.manage') }}</h5>
                                                    <a href="{{ route('manage.cp.video.edit', ['property_id'=> $property_id, 'id'=> $video->id ]) }}">{{ __('common.edit') }}</a>
                                                    @if (optional($video->platformAlivod)->source_url)
                                                        <a href="javascript:void(0);" onclick="showDownload(this)" download_url="{{ $video->source_url }}">{{ __('common.download') }}</a>
                                                    @endif
                                                    </li>
                                                    <li class="group">
                                                    <h5>{{ __('manage/cp/contents/videos.ai_function') }}</h5>
                                                        @php
                                                            $resultAnalyzeUrl = route('manage.cp.video.analyze.results', ['property_id'=> $property_id, 'id'=> $video->id ]);
                                                        @endphp
                                                        @if ($video->analyze_status == \App\Models\Entry::ANALYZE_STATUS_NOT_START)
                                                            <a href="{{ route('manage.cp.video.analyze',['property_id'=> $property_id, 'id'=> $video->id]) }}" class="analyze-confirm" data-toggle="modal" data-target="#analyze">{{ __('manage/cp/contents/videos.analyze') }}</a>
                                                            <a href="#" class="analyze-disable">{{ __('manage/cp/contents/videos.results') }}</a>
                                                        @elseif ($video->analyze_status == \App\Models\Entry::ANALYZE_STATUS_PROCESSING)
                                                            <a href="#" class="analyze-disable">{{ __('manage/cp/contents/videos.analyze') }}</a>
                                                            <a href="#" class="analyze-disable">{{ __('manage/cp/contents/videos.results') }}</a>
                                                        @elseif ($video->analyze_status == \App\Models\Entry::ANALYZE_STATUS_READY)
                                                            <a href="#" class="analyze-disable">{{ __('manage/cp/contents/videos.analyze') }}</a>
                                                            <a href="{{ $resultAnalyzeUrl }}">{{ __('manage/cp/contents/videos.results') }}</a>
                                                        @elseif ($video->analyze_status == \App\Models\Entry::ANALYZE_STATUS_FAILED)
                                                            <a href="{{ route('manage.cp.video.analyze',['property_id'=> $property_id, 'id'=> $video->id]) }}" class="analyze-confirm" data-toggle="modal" data-target="#analyze">{{ __('manage/cp/contents/videos.analyze') }}</a>
                                                            <a href="#"  class="analyze-disable">{{ __('manage/cp/contents/videos.results') }}</a>
                                                        @endif
                                                        <a href="#" class="analyze-disable">{{ __('manage/cp/contents/videos.ai_translate') }}</a>
                                                    </li>
                                                    <li>
                                                    <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="{{ $property_id }}_{{ $video->id }}" onclick="setFormId(this);">{{ Form::open(['url' => route('manage.cp.video.delete', ['property_id'=> $property_id, 'id'=> $video->id ]), 'method' => 'DELETE', 'id' => $property_id . "_" . $video->id]) }}{{ __('manage/cp/contents/videos.delete') }}{{ Form::close() }}</a>
                                                    </li>
                                                  </ul>
                                                  <div class="alert alert-danger" role="alert" style="display:none;"></div>
                                             </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- MODAL -->
                            <div id="modal"></div>
                            
                            <div class="row">
                              <div class="col-md-12"><span>{{ __('manage/cp/contents/videos.have_chosen') }} <span id="video-selected-count">0</span> {{ __('manage/cp/contents/videos.video') }}</span></div>
                            </div>

                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($videos->count()>0)
                                        {{
                                                __(
                                                    'manage/cp/contents/videos.showing_from_to_videos',
                                                    [
                                                        'from'=>$videos->firstItem(),
                                                        'to'=>$videos->lastItem(),
                                                        'total'=>$videos->total()
                                                    ]
                                                )
                                            }}
                                        @else
                                            {{ __('manage/cp/contents/videos.no_videos') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $videos->appends(request()->input())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="analyze" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.analyze_confirm') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>{{ __('manage/cp/contents/videos.analyze_alert') }}</div>
                                    {!! Form::open(['method'=>'POST','id'=>'video-analyze']) !!}
                                    {!! Form::close() !!}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="$('#video-analyze').submit();">{{ __('manage/cp/contents/videos.analyze_proceed') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.delete_video') }}</h5>
                                </div>
                                <div class="modal-body">
                                    <div>{{ __('manage/cp/contents/videos.action_is_not_reversible') }}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin/common.back') }}</button>
                                    <button type="button" class="btn btn-secondary" data-type="single_delete" onclick="nextStep();">{{ __('admin/common.continue') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.delete_video') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>{{ __('manage/cp/contents/videos.deleting_a_video') }}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="delete-video-button" data-form-id="" onclick="deleteVideo(this);">{{ __('manage/cp/contents/videos.delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="stop-livestream" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.stop_livestream') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>{{ __('manage/cp/contents/videos.this_action_is_not_reversible') }}<br />
                                    {{ __('manage/cp/contents/videos.resume_a_stopped_livestream') }}<br /><br />

                                    {{ __('manage/cp/contents/videos.want_to_proceed') }}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="stop-livestream-button" data-link="" onclick="stopLivestream(this);">{{ __('manage/cp/contents/videos.stop_livestream') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="bulk-delete" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        {{ Form::open(['url'=>route('manage.cp.video.delete.bulk', $property_id)]) }}
                        {{ Form::hidden('video_ids','') }}
                        {{ Form::hidden('select_all','') }}
                        {{ Form::hidden('all_video_ids', $all_video_ids) }}
                        {{ method_field('DELETE') }}
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                             <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel">{{ __('manage/cp/contents/playlists.delete_playlist') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div>{{ __('manage/cp/contents/videos.deleting_a_video') }}</div>
                                </div>
                                 <div class="modal-footer">
                                  <button type="submit" class="btn btn-secondary">{{ __('manage/cp/contents/videos.delete') }}</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div class="modal fade" id="modal-download-url" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.download_video') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="wizard-alert alert alert-success hidden" role="alert" id="copy-succ-message">
                                        <span>{{ __('manage/cp/contents/videos.successful_copy') }}</span>
                                    </div>
                                    <div class="ibox-content">
                                        <p>{{ __('manage/cp/contents/videos.download_tips') }}</p>
                                        <form class="form-horizontal">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control" id="source_url" readonly value="">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="copyToClipboard()">{{ __('common.copy') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form method="post" action="{{ route('manage.cp.video.review.bulk', $property_id) }}" id="approve-form">
                        {{ csrf_field() }}
                        <input type="hidden" name="video_ids" value="">
                        {{ Form::hidden('select_all', '') }}
                        {{ Form::hidden('all_video_ids', $all_video_ids) }}
                    </form>

                    {{ Form::open(['url' => route('manage.cp.video.export.bulk', $property_id), 'id' => 'export-form']) }}
                    {{ Form::hidden('video_ids','') }}
                    {{ Form::hidden('select_all','') }}
                    {{ Form::hidden('all_video_ids', $all_video_ids) }}
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    var videofeed = {
        confirm:'@lang('videofeed.confirm_delete')'
    };

    $(document).ready(function(){

        $('[data-toggle="tooltip"]').tooltip();
        $('.analyze-confirm').click(function(){
            $("#analyze .modal-body > form").attr("action", $(this).attr('href'));
        });

        $('#date_sort').click(function(){
            if($(this).hasClass('sorting') || $(this).hasClass('sorting_desc')) {
                $('.date_sort').val('asc');
            } else {
                $('.date_sort').val('desc');
            }
            $("#videos_form").submit();
        });

        $("td input[type=checkbox]").click(function () {
            if ($(this).is(":checked")) {
                $('#video-selected-count').html(parseInt($('#video-selected-count').html()) + 1);
            } else {
                if ($(".select-all input[type=checkbox]").is(":checked")) {
                    var this_page_select = 0;
                    $('.video-checkbox').each(function(){
                        if ($(this).is(':checked')) {
                            this_page_select++;
                        }
                    });
                    $(".select-all input[type=checkbox]").prop('checked', false);
                    $('#video-selected-count').html(this_page_select);
                } else {
                    $('#video-selected-count').html(parseInt($('#video-selected-count').html()) - 1);
                }
                $('#select-all').val(0);
                $('#select-all').trigger('change');
            }
        });

        $(".select-all input[type=checkbox]").click(function() {
            if($(this).is(":checked")) {
                $("td input[type=checkbox]").prop("checked", true);
                var select_all = $('#select-all').val();
                if (0 == select_all) {
                    $('#video-selected-count').html({{ $videos->count() }});
                } else if (1 == select_all) {
                    $('#video-selected-count').html({{ $videos->total() }});
                }
            } else {
                $("td input[type=checkbox]").prop("checked", false);
                $('#video-selected-count').html(0);
            }
        });

        $("#import").click(function() {
            $(this).parent().find("input[type=file]").click()
        })

        $("#uploadCsv").change(function () {
            if ($(this).val()) {
                $("#import-form").submit();
            }
        });

        $('.editable-title i.fa-edit').click(function () {
            $(this).parent().siblings('.editable-title-input').children('input:eq(1)').val($(this).siblings('a').text());
            $(this).parent().siblings('.editable-title-input').show()
            $(this).parent().hide();
        });
        $('.editable-title-input i.fa-times').click(function () {
            $(this).parent().siblings('.editable-title').show()
            $(this).parent().hide()
        });
        $('.editable-title-input i.fa-check').click(function () {
            _this = $(this)
            var videoId = _this.siblings('input:eq(0)').val()
            var videoName = _this.siblings('input:eq(1)').val()
            var url = '/manage/' + '{{ $property_id }}' + '/cp/videos/edit/' + videoId
            $.ajax({
                type: 'POST',
                url: url,
                data: { title: videoName, isQuickEdit: true },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data)
                {
                    console.log(data.message);
                    _this.parent().siblings('.editable-title').children('a').text(videoName);
                    _this.parent().siblings('.editable-title').show()
                    _this.parent().hide()
                },
                error: function (data) {
                    console.log('An error occurred.');
                    alert(data.responseJSON.message);
                }
            });
        });
    });
    function setFormId(element) {
        var formId = element.getAttribute('data-form-id');
        $('#delete-video-button').attr('data-form-id', formId);
        $('#delete_confirm').modal('show').attr('data-type', 'single_delete');
    }
    function nextStep() {
        $('#delete_confirm').modal('hide');
        var type = $('#delete_confirm').attr('data-type');
        if (type == 'bulk_delete') {
            $('#bulk-delete').modal('show');
        } else {
            $('#delete').modal('show');
        }
    }
    function deleteVideo(element) {
        var formId = element.getAttribute('data-form-id');
        $('#' + formId).submit();
    }

    function setStopLivestream(element) {
        var link = element.getAttribute('data-link');
        $('#stop-livestream-button').attr('data-link', link);
        $('#stop-livestream').modal('show');
    }
    function stopLivestream(element) {
        var link = element.getAttribute('data-link');
        location.href = link;
    }

    $('#select-all').change(function () {
        var this_page_select = 0;
        $('.video-checkbox').each(function(){
            if ($(this).is(':checked')) {
                this_page_select++;
            }
        });
        if ($(".select-all input[type=checkbox]").is(":checked")) {
            if (1 == $(this).val()) {
                $('#video-selected-count').html(parseInt({{ $videos->total() }}) - parseInt({{ $videos->count() }}) + this_page_select );
            } else {
                $('#video-selected-count').html(this_page_select);
            }
        } else {
            $('#video-selected-count').html(this_page_select);
        }
    });

    function bulk_action(bulk_type) {
        var video_ids = '';
        var select_all = $('#select-all').val();

        if (!$(".select-all input[type=checkbox]").is(":checked")) {
            select_all = 0;
        }

        $('.video-checkbox').each(function(){
            if ($(this).is(':checked')) {
                if (video_ids) {
                    video_ids += ',' + $(this).val();
                } else {
                    video_ids += $(this).val();
                }
            }
        });
        
        if (!video_ids) {
            alert("{{ __('manage/cp/contents/videos.no_record_selected') }}");
            return false;
        }

        $("input[name = 'select_all']").val(select_all);

        if (bulk_type == 1) {
            $("input[name = 'video_ids']").val(video_ids);
            $('#delete_confirm').modal('show').attr('data-type', 'bulk_delete');
        } else if (bulk_type == 2) {
            $("input[name = 'video_ids']").val(video_ids);
            $('#export-form').submit();
        } else {
            $("input[name = 'video_ids']").val(video_ids);
            $('#approve-form').submit();
        }
    }

    function showDownload(obj)
    {
        $("#copy-succ-message").addClass('hidden');
        $("#source_url").val($(obj).attr('download_url'));
        $('#modal-download-url').modal('show');
    }

    function copyToClipboard() {
        $('#source_url').select();
        document.execCommand("copy");
        $("#copy-succ-message").removeClass('hidden');
    }
</script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css">
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript">
    $(function() {
        function cb(start, end) {
            $('#reportrange span').html(start.format('L') + ' - ' + end.format('L'));
            location.href = '{{ url()->current() }}' + '?start_date='+start.format('L') + '&end_date='+end.format('L') + '&status={{ $status }}' + '&playlist={{ $playlist_id }}' + '&search={{ $search }}' + '&sort={{ $sort }}';
        }
        $('#reportrange').daterangepicker({
            maxDate: moment(),
            locale: {
                applyLabel: '{{ __('common.confirm') }}',
                cancelLabel: '{{ __('common.cancel') }}',
            }
        }, cb);

        $('.select2').select2({
            minimumResultsForSearch: Infinity,
        });
    });
</script>
<script type="text/javascript" src="{!! asset('js/videoplayer/videoplayer.js') !!}"></script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
