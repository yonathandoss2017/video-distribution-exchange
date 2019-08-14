@extends('partials.layout_sp')

@push('title')
   {{ __('manage/sp/content/video.page_title') }} | {{ __('app.title') }}
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
                            <div class="title">{{ __('manage/sp/content/video.page_title') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('common.bulk_action') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" onclick="bulk_action(1);">{{ __('manage/sp/content/video.download') }}</a></li>
                                    </ul>
                                </div>
                                {!! Form::open(['url'=>route('manage.sp.videos', $property_id),'method'=>'GET', 'id' => 'videos_form']) !!}
                                <div class="status-float m-r">
                                    <div id="reportrange" class="form-control">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span>{{ __('manage/cp/contents/videos.all_time') }}</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('manage/sp/content/video.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ url()->current() .'?playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/sp/common.all') }}</a></li>
                                        <li><a href="{{ url()->current() . '?status='.\App\Models\Entry::STATUS_READY.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}">{{ __('manage/sp/content/video.ready') }}</a></li>
                                    </ul>
                                </div>
                                <div class="input-group">
                                    @foreach(request()->except('search') as $key=>$value)
                                        {!! Form::hidden($key, $value) !!}
                                    @endforeach
                                    <input type="text" placeholder="{{ __('manage/sp/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                                </div>
                                <input type="hidden" class="date_sort" name="sort" value="{{ $sort }}">
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-md-12 search-result">
                            @if($playlist)
                                {{ __('manage/sp/common.playlist') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $pp && $pp->playlist_name ? $pp->playlist_name : $playlist->name }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&start_date='.$start_date.'&end_date='.$end_date.'&search='.$search.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if($start_date || $end_date)
                                {{ __('common.date') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $start_date }} - {{ $end_date }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&playlist='.$playlist_id.'&search='.$search.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if($search)
                                {{ __('manage/sp/common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                            @endif
                            @if(in_array(strtolower($status), [
                                \App\Models\Entry::STATUS_READY]))

                                {{ __('manage/sp/common.status') }}
                                <div class="search-result-info filter-margin-right">
                                    <span class="search-result-text">{{ ucfirst(__('manage/sp/content/video.'. strtolower($status))) }}</span>
                                    <span class="search-close"><a href="{{ url()->current(). '?search='.$search.'&playlist='.$playlist_id.'&start_date='.$start_date.'&end_date='.$end_date.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="ibox">

                    <div class="ibox-content">
                        <div class="video-list video-list-cp spwp">
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
                                            <option value="0">当前页面</option>
                                            <option value="1">所有页面</option>
                                        </select>
                                    </th>
                                    <th class="video-name">{{ __('manage/sp/common.title') }} / {{ __('manage/sp/common.playlist') }}</th>
                                    <th class="duration">{{ __('manage/sp/content/video.duration') }}</th>
                                    <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort">{{ __('manage/sp/common.published_at') }}</th>
                                    <th class="status">{{ __('manage/sp/common.status') }}</th>
                                    <th class="right">{{ __('manage/sp/common.actions') }}</th>
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
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video -> id }}">
                                                    <img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($video, $property_id, $video->getSpEntryTimestamp($property_id), 240) }}" alt="" onclick="getPlayer('{{ $video -> id }}', '{{ $property_id }}')">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="video-name">
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video -> id }}"
                                               onclick="getPlayer('{{ $video -> id }}', '{{ $property_id }}')">{{ $video->name }}
                                            </a>
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
                                            @else
                                                {{gmdate('i:s', $video->duration)}}
                                            @endif
                                        </td>
                                        <td class="date">
                                            <div id="{{ $video->id }}-{{ $video->published_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $video->id }}" dt="{{ $video->published_at->timestamp }}"></small>
                                        </td>
                                        <td class="status">
                                            @if ($video->status == \App\Models\Entry::STATUS_READY)
                                                <span class="label label-active">{{ __('manage/sp/content/video.ready') }}</span>
                                            @endif
                                        </td>
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('manage/sp/common.actions') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                    <ul class="dropdown-menu">
                                                      <li class="group">
                                                      <h5>{{ __('manage/sp/common.manage') }}</h5>
                                                      <a href="{{ route('manage.sp.video.edit', ['property_id'=> $property_id, 'id'=> $video->id ]) }}">{{ __('manage/sp/common.edit') }}</a>
                                                      <a href="javascript:void(0);" onclick="showDownload(this)" download_url="{{ $video->source_url }}">{{ __('common.download') }}</a>
                                                      </li>
                                                    </ul>
                                                    <div class="alert alert-info" role="alert" style="display:none;"></div>
                                             </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <!-- MODAL -->
                            <div id="modal"></div>

                            <div class="row">
                              <div class="col-md-12"><span>已选择 <span id="video-selected-count">0</span> 个视频</span></div>
                            </div>

                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($videos->count()>0)
                                            {{
                                                    __(
                                                        'manage/sp/content/video.videos_pagination',
                                                        [
                                                            'from'=>$videos->firstItem(),
                                                            'to'=>$videos->lastItem(),
                                                            'total'=>$videos->total()
                                                        ]
                                                    )
                                                }}
                                        @else
                                            {{ __('manage/sp/content/video.empty_videos') }}
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
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="codes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog widget" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('manage/sp/content/video.embed_code') }} <span class="embed-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body a-pl-2 left">
                   <div class="embed-code" id="embed-code"></div>
                </div>
                <div class="modal-footer">
                    <div class="form-save">
                        <button type="submit" class="btn btn-primary" class="close" data-dismiss="modal" aria-label="Close" id="copy-button" data-clipboard-target="#embed-code">{{ __('manage/sp/common.copy') }}</button>
                    </div>
                </div>
            </div>
        </div>
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

    {{ Form::open(['url' => route('manage.sp.video.download.bulk', $property_id), 'id' => 'down-form']) }}
    {{ Form::hidden('video_ids','') }}
    {{ Form::hidden('select_all','') }}
    {{ Form::hidden('start_date', $start_date) }}
    {{ Form::hidden('end_date', $end_date) }}
    {{ Form::hidden('status', $status) }}
    {{ Form::hidden('search', $search) }}
    {{ Form::hidden('all_video_ids', $all_video_ids) }}
    {{ Form::close() }}
@stop

@push('js')
<script>

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
        $("input[name = 'video_ids']").val(video_ids);
        
        if (1 == bulk_type) {
            $('#down-form').submit();
        }
    }

    var videofeed = {
        confirm:'@lang('manage/sp/content/video.confirm_delete')'
    };

    $(document).ready(function(){

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

        $('a.modal-embed').on('click', function () {
            playlistName = $(this).attr('video-name');
            $('.embed-name').html(playlistName);
        });

        $('#copy-button').on('click', function () {
            new Clipboard('#copy-button');
        });

        $('.modal-header .close').on('click', function () {
            $('.embed-code').html('');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove('');
        });

        $('#date_sort').click(function(){
            if($(this).hasClass('sorting') || $(this).hasClass('sorting_desc')) {
                $('.date_sort').val('asc');
            } else {
                $('.date_sort').val('desc');
            }
            $("#videos_form").submit();
        });

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
    });

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
    });
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="{!! asset('js/videoplayer/videoplayer.js') !!}"></script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            minimumResultsForSearch: Infinity,
        });
    });
</script>
@endpush
