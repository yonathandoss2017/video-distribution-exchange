@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('common.playlists') }}
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
                            <div class="title">{{ __('common.playlists') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                @can('create-cp-playlist', $property_id)
                                <a href="{{ route('manage.cp.playlists.create', $property_id) }}" class="btn btn-normal btn-m m-r">{{ __('manage/cp/contents/playlists.new_playlist') }}</a>
                                @endcan
                                <div class="status-float">
                                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('common.bulk_action') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                    <ul class="dropdown-menu">
                                        @if (config('features.content_review'))
                                            <form method="post" action="{{ route('manage.cp.playlists.review.request', $property_id) }}" id="approve-form">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="playlist_ids" value="">
                                                {{ Form::hidden('select_all','') }}
                                                {{ Form::hidden('search', $search) }}
                                                {{ Form::hidden('all_playlist_ids', $all_playlist_ids) }}
                                            </form>
                                            <li><a href="javascript:void(0)" onclick="bulk_action(0);">{{ __('manage/cp/contents/playlists.request_review') }}</a></li>
                                        @endif
                                        <li><a href="javascript:void(0)" data-toggle="modal" onclick="bulk_action(1);">{{ __('manage/cp/contents/playlists.delete') }}</a></li>
                                    </ul>
                                </div>
                                {!! Form::open(['route'=>['manage.cp.playlists.index', $property_id],'method'=>'GET','class' => 'playlist-search', 'id' => 'playlists_form']) !!}
                                <div class="input-group sr">
                                    <input type="text" placeholder="{{ __('common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}" >
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </span>
                                </div>
                                <input type="hidden" class="date_sort" name="sort" value="{{ $sort }}">
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-md-12 search-result">
                            @if($search)
                                {{ __('common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current().'?sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
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
                                            <option value="0">{{ __('manage/cp/contents/playlists.current_page') }}</option>
                                            <option value="1">{{ __('manage/cp/contents/playlists.all_page') }}</option>
                                        </select>
                                    </th>
                                    <th class="playlist-title" style="width: 30%;">{{ __('common.playlist') }}</th>
                                    <th>{{ __('common.videos') }}</th>
                                    <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort">{{ __('common.last_updated') }}</th>
                                    <th>{{ __('manage/cp/contents/playlists.marketplace') }}</th>
                                    <th class="status">{{ __('common.status') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($playlists as $playlist)
                                    <tr>
                                        <td>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input playlist-checkbox" value="{{ $playlist->id }}">
                                                <span class="custom-control-indicator"></span>
                                            </label>
                                        </td>
                                        <td class="image">
                                            <div class="video-img">
                                                <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id, null, 120) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="playlist-title ptl editable" style="width: 35%;">
                                            <div class="editable-title">
                                                <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ $playlist->name }}</a>
                                                <i class="fa fa-edit"></i>
                                            </div>
                                            <div class="editable-title-input hidden">
                                                <input type="hidden" value="{{ $playlist->id }}">
                                                <input class="form-control" type="text" value="">
                                                <i class="fa fa-check"></i>
                                                <i class="fa fa-times"></i>
                                            </div>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $playlist->entries_count }}</span> <small>{{ __('common.videos') }}</small>
                                        </td>
                                        <td class="date">
                                            <div id="{{ $playlist->id }}-{{ $playlist->updated_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $playlist->id }}" dt="{{ $playlist->updated_at->timestamp }}"></small>
                                        </td>
                                        <td>
                                            @if($playlist->publish_status == \App\Models\Playlist::PUBLISH_STATUS_PUBLISHED)
                                            <span class="label label-active">{{ __('manage/organization/playlists.published') }}</span>
                                            @elseif($playlist->publish_status == \App\Models\Playlist::PUBLISH_STATUS_REVIEW)
                                            <span class="label label-orange">{{ __('manage/cp/contents/playlists.pending') }}</span>
                                            @elseif($playlist->publish_status == \App\Models\Playlist::PUBLISH_STATUS_REJECTED)
                                                <span class="label label-rejected">{{ __('manage/organization/playlists.rejected') }}</span>
                                            @elseif($playlist->publish_status == \App\Models\Playlist::PUBLISH_STATUS_UNPUBLISH)
                                                <span class="label label-grey">{{ __('manage/organization/playlists.unpublished') }}</span>
                                            @endif
                                        </td>
                                        <td class="status">
                                            @if($playlist->status == \App\Models\Playlist::STATUS_READY)
                                                <span class="label label-active">{{ __('manage/cp/contents/playlists.ready') }}</span>
                                            @elseif($playlist->status == \App\Models\Playlist::STATUS_REJECTED)
                                                <span class="label label-rejected">{{ __('manage/cp/contents/playlists.rejected') }}</span>
                                            @elseif($playlist->status == \App\Models\Playlist::STATUS_PENDING)
                                                <span class="label label-orange">{{ __('manage/cp/contents/playlists.pending') }}</span>
                                            @elseif($playlist->status == \App\Models\Playlist::STATUS_DRAFT)
                                                <span class="label label-grey">{{ __('manage/cp/contents/playlists.draft') }}</span>
                                            @endif
                                        </td>
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                            <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                  <ul class="dropdown-menu">
                                                    <li class="group">
                                                    <h5>{{ __('manage/cp/contents/playlists.manage') }}</h5>
                                                    <a href="{{ route('manage.cp.playlists.edit', [ $property_id, $playlist->id ]) }}">{{ __('common.edit') }}</a>
                                                    @if($playlist->status == \App\Models\Playlist::STATUS_READY)
                                                        <a href="{{ route('manage.cp.playlists.publish', [ $property_id, $playlist->id ]) }}">{{ __('common.publish') }}</a>
                                                    @endif
                                                    </li>
                                                    <li class="group">
                                                    <h5>{{ __('manage/cp/contents/playlists.list') }}</h5>
                                                    <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ __('common.videos') }}</a>
                                                    </li>
                                                    <li><a href="javascript:void(0)" data-toggle="modal" data-target="#delete_confirm" onclick="deletePlaylist({{ $playlist->id }})">{{ __('manage/cp/contents/playlists.delete') }}</a></li>
                                                  </ul>
                                             </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="row">
                              <div class="col-md-12"><span>{{ __('manage/cp/contents/playlists.have_chosen') }} <span id="playlist-selected-count">0</span> {{ __('manage/cp/contents/playlists.playlists') }}</span></div>
                            </div>

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
                <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog s-width" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/playlists.delete_playlist') }}</h5>
                            </div>
                            <div class="modal-body">
                                <div>{{ __('manage/cp/contents/playlists.action_is_not_reversible') }}</div>
                            </div>
                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin/common.back') }}</button>
                                 <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#delete" onclick="nextStep();">{{ __('admin/common.continue') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    {{ Form::open(['url'=>route('manage.cp.playlists.destroy',[ $property_id, 1])]) }}
                    {{ Form::hidden('playlist_id','') }}
                    {{ Form::hidden('select_all','') }}
                    {{ Form::hidden('search', $search) }}
                    {{ Form::hidden('all_playlist_ids', $all_playlist_ids) }}
                    {{ method_field('DELETE') }}
                    <div class="modal-dialog s-width" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/playlists.delete_playlist') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div>{{ __('manage/cp/contents/playlists.deleting_a_playlist') }}</div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-secondary">{{ __('manage/cp/contents/playlists.delete') }}</button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script>

    function deletePlaylist(id) {
        $("input[name = 'playlist_id']").val(id);
    }

    function nextStep() {
        $('#delete_confirm').modal('hide');
    }

    function bulk_action(bulk_type) {
        var playlist_ids = '';
        var select_all = $('#select-all').val();
        if (!$(".select-all input[type=checkbox]").is(":checked")) {
            select_all = 0;
        }

        $('.playlist-checkbox').each(function(){
            if ($(this).is(':checked')) {
                if (playlist_ids) {
                    playlist_ids += ',' + $(this).val();
                } else {
                    playlist_ids += $(this).val();
                }
            }
        });

        if (!playlist_ids) {
            alert("{{ __('manage/cp/contents/playlists.no_record_selected') }}");
            return false;
        }

        $("input[name = 'select_all']").val(select_all);

        if (bulk_type) {
            $("input[name = 'playlist_id']").val(playlist_ids);
            $('#delete_confirm').modal('show');
        } else {
            $("input[name = 'playlist_ids']").val(playlist_ids);
            $('#approve-form').submit();
        }
    }

    $(document).ready(function(){
        $('.select2').select2({
            minimumResultsForSearch: Infinity,
        });

        $('#date_sort').click(function(){
            if($(this).hasClass('sorting') || $(this).hasClass('sorting_desc')) {
                $('.date_sort').val('asc');
            } else {
                $('.date_sort').val('desc');
            }
            $("#playlists_form").submit();
        });

        $("td input[type=checkbox]").click(function () {
            if ($(this).is(":checked")) {
                $('#playlist-selected-count').html(parseInt($('#playlist-selected-count').html()) + 1);
            } else {
                if ($(".select-all input[type=checkbox]").is(":checked")) {
                    var this_page_select = 0;
                    $('.playlist-checkbox').each(function(){
                        if ($(this).is(':checked')) {
                            this_page_select++;
                        }
                    });
                    $(".select-all input[type=checkbox]").prop('checked', false);
                    $('#playlist-selected-count').html(this_page_select);
                } else {
                    $('#playlist-selected-count').html(parseInt($('#playlist-selected-count').html()) - 1);
                }
                $('#select-all').val(0);
                $('#select-all').trigger('change');
            }
        });

        $('#select-all').change(function () {
            var this_page_select = 0;
            $('.playlist-checkbox').each(function(){
                if ($(this).is(':checked')) {
                    this_page_select++;
                }
            });
            if ($(".select-all input[type=checkbox]").is(":checked")) {
                if (1 == $(this).val()) {
                    $('#playlist-selected-count').html(parseInt({{ $playlists->total() }}) - parseInt({{ $playlists->count() }}) + this_page_select );
                } else {
                    $('#playlist-selected-count').html(this_page_select);
                }
            } else {
                $('#playlist-selected-count').html(this_page_select);
            }
        });

        $(".select-all input[type=checkbox]").click(function() {
            if($(this).is(":checked")) {
                $("td input[type=checkbox]").prop("checked", true)
                $("td input[type=checkbox]").prop("checked", true);
                var select_all = $('#select-all').val();
                if (0 == select_all) {
                    $('#playlist-selected-count').html({{ $playlists->count() }});
                } else if (1 == select_all) {
                    $('#playlist-selected-count').html({{ $playlists->total() }});
                }
            } else {
                $("td input[type=checkbox]").prop("checked", false)
                $("td input[type=checkbox]").prop("checked", false);
                $('#playlist-selected-count').html(0);
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
            var playlistId = _this.siblings('input:eq(0)').val()
            var playlistName = _this.siblings('input:eq(1)').val()
            var url = '/manage/' + '{{ $property_id }}' + '/cp/playlists/' + playlistId
            $.ajax({
                type: 'POST',
                url: url,
                data: { name: playlistName, isQuickEdit: true, _method: 'PUT' },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data)
                {
                    console.log(data.message);
                    _this.parent().siblings('.editable-title').children('a').text(playlistName);
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
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
