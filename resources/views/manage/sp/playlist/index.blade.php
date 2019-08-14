@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/content/playlist.page_title') }} | {{ __('app.title') }}
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
                            <div class="title">{{ __('manage/sp/content/playlist.page_title') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                {!! Form::open(['route'=>['manage.sp.playlists.index', $property_id],'method'=>'GET', 'id' => 'playlists_form']) !!}
                                <div class="input-group sr">
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
                            @if($search)
                                {{ __('manage/sp/common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current().'?&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
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
                                    <th class="image">{{ __('manage/sp/common.thumbnail') }}</th>
                                    <th class="playlist-title ptl">{{ __('manage/sp/common.playlist') }}</th>
                                    <th style="width: 100px;">{{ __('manage/sp/common.videos') }}</th>
                                    <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort">{{ __('manage/sp/common.last_updated') }}</th>
                                    <th>{{ __('manage/sp/common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $playlists as $playlist)
                                    @php
                                        $tod = $playlist->activeTods->first();
                                    @endphp
                                    <tr>
                                        <td class="image">
                                            <div class="video-img">
                                                <a href="{{ route('manage.sp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id, \Carbon\Carbon::parse($playlist->published_at)->timestamp) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="playlist-title pt">
                                            <a href="{{ route('manage.sp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
                                                {{ \App\Services\Serve\PlaylistDetailsService::getName($playlist, $property_id) }}
                                            </a>
                                            <br>
                                            <small>{{ $playlist->contentProvider->organization->name }} » {{ $playlist->contentProvider->name }}</small>
                                            <br>
                                            @php
                                            $todLinkParam = $tod ? [$property_id, $tod->id] : [$property_id, $playlist->contentProvider->internalTod->id];
                                            @endphp
                                            <small>{{ __('manage/sp/content/playlist.terms') }}: <a href="{{ route('manage.sp.tod.show', $todLinkParam) }}" class="terms-link">
                                                {{ \App\Services\Serve\PlaylistDetailsService::getActiveTodFromPlaylist($playlist, $property_id)->name }}
                                            </a></small>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $playlist->entries_count }} </span> <small>{{ __('manage/sp/common.videos') }}</small>
                                        </td>
                                        <td>
                                            <span id="{{ $playlist->id }}-{{ $playlist->updated_at->timestamp }}"></span><br/>
                                            <small class="timestamp" id="{{ $playlist->id }}" dt="{{ $playlist->updated_at->timestamp }}"></small>
                                        </td>
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('manage/sp/common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li class="group">
                                                        <h5>{{ __('manage/sp/common.manage') }}</h5>
                                                        <a href="{{ route('manage.sp.playlist.edit', [ $property_id, $playlist->id ]) }}">{{ __('manage/sp/common.edit') }}</a>
                                                    </li>
                                                    <li class="group">
                                                        <h5>{{ __('manage/sp/common.list') }}</h5>
                                                        <a href="{{ route('manage.sp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ __('manage/sp/common.videos') }}</a>
                                                    </li>
                                                    @if ($tod)
                                                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#delete" data-delete-form-id="{{ $property_id }}_{{ $playlist->id }}" onclick="setDeleteFormId(this)">{{ Form::open(['url' => route('manage.sp.playlists.destroy', ['property_id'=> $property_id, 'id'=> $playlist->id ]), 'method' => 'DELETE', 'id' => "delete_" . $property_id . "_" . $playlist->id]) }}{{ __('manage/sp/common.delete') }}{{ Form::close() }}</a></li>
                                                    @endif
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
                                                    'manage/sp/content/playlist.playlists_pagination',
                                                    [
                                                        'from'=>$playlists->firstItem(),
                                                        'to'=>$playlists->lastItem(),
                                                        'total'=>$playlists->total()
                                                    ]
                                                )
                                            }}
                                        @else
                                            {{ __('manage/sp/content/playlist.empty_playlists') }}
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
                <div class="modal fade" id="codes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog widget" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('manage/sp/content/playlist.embed_code') }} <span class="embed-name"></span></h5>
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
                <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog s-width" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/sp/content/playlist.delete_playlist') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div>{{ __('manage/sp/content/playlist.delete_confirm') }}? <br/><br/>{{ __('manage/sp/content/playlist.delete_note') }}.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-secondary" id="delete-playlist-button" data-delete-form-id="" onclick="deletePlaylist(this);">{{ __('manage/sp/common.delete') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    function setDeleteFormId(element) {
        var formId = element.getAttribute('data-delete-form-id');
        $('#delete-playlist-button').attr('data-delete-form-id', formId);
        $('#delete').modal('show');
    }
    function deletePlaylist(element) {
        var formId = element.getAttribute('data-delete-form-id');
        $('#delete_' + formId).submit();
    }

    $(document).ready(function(){

        $('a.modal-embed').on('click', function () {
            playlistName = $(this).attr('playlist-name');
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
            $("#playlists_form").submit();
        });
    });
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
