@extends('partials.layout_home')

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
                                {!! Form::open(['route'=>['manage.organization.playlists.index'], 'method'=>'GET', 'class' => 'playlist-search', 'id' => 'playlists_form']) !!}
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
                                    <th class="image"></th>
                                    <th class="playlist-title" style="width: 30%;">{{ __('common.playlist') }}</th>
                                    <th>{{ __('common.videos') }}</th>
                                    <th>{{ __('common.owner_account') }}</th>
                                    <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort">{{ __('common.last_updated') }}</th>
                                    <th>{{ __('manage/cp/contents/playlists.marketplace') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($playlists as $playlist)
                                    <tr>
                                        <td class="image">
                                            <div class="video-img">
                                                <a href="{{ route('manage.organization.videos.index', ['playlist' => $playlist->id]) }}">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $playlist->property_id, null, 120) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="playlist-title">
                                            <a href="{{ route('manage.organization.videos.index', ['playlist' => $playlist->id]) }}">{{ $playlist->name }}</a>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $playlist->entries_count }}</span> <small>{{ __('common.videos') }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $playlist->contentProvider->name }}</div>
                                            <small>{{ $playlist->creator->name }}</small>
                                        </td>
                                        <td class="date">
                                            <div id="{{ $playlist->id }}-{{ $playlist->updated_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $playlist->id }}" dt="{{ $playlist->updated_at->timestamp }}"></small>
                                        </td>
                                        <td class="status">
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
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                <ul class="dropdown-menu">
                                                    @if($playlist->status == \App\Models\Playlist::STATUS_READY)
                                                    <li>
                                                        <a href="{{ route('manage.organization.playlists.publish.show', $playlist->id) }}">{{ __('common.publish') }}</a>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <a href="{{ route('manage.organization.videos.index', ['playlist' => $playlist->id]) }}">{{ __('common.videos') }}</a>
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
                    {{ Form::open(['url' => route('manage.organization.playlists.destroy')]) }}
                    {{ Form::hidden('playlist_id','') }}
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
<script>

    function deletePlaylist(id) {
        $("input[name = 'playlist_id']").val(id);
    }

    function nextStep() {
        $('#delete_confirm').modal('hide');
    }

    $(document).ready(function(){

        $('#date_sort').click(function(){
            if($(this).hasClass('sorting') || $(this).hasClass('sorting_desc')) {
                $('.date_sort').val('asc');
            } else {
                $('.date_sort').val('desc');
            }
            $("#playlists_form").submit();
        });
    });
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
