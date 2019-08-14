@extends('partials.layout_dpp')

@push('title')
iVideoExchange | iVideoAdd Video
@endpush

@section('content')
<!-- Begin page content -->
@if (!$ready)
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3 text-center">
            <div class="alert alert-warning" role="alert">
                {!! __('dpp.note_before_publish') !!}
            </div>
        </div>
    </div>
</div>
@endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
        <div class="title-header">
            @if ($playlist->dpp_status == \App\Models\Playlist::DPP_STATUS_PROCESSING)
            <a href="#" class="btn btn-normal btn-m" data-toggle="modal" data-target="{{ $ready?'#publish':'#incomplete'}}">{{ __('dpp.publish_to_cp') }}</a>
            @endif
            <div class="title">{{ __('dpp.dpp_playlist', ['name'=>$playlist->name]) }}</div>
        </div>

        @include('partials.dpp_playlist_summary')

        <div class="min-menu m-t-2">
            <div class="min-menu row">
                <div class="col-md-6"><div class="title">{{ __('dpp.dpp_videos') }}</div></div>
                <div class="col-md-6">
                    <div class="right-panel">
                        <div class="status-float">
                            <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('dpp.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('dpp.request.show', $playlist->id) }}">{{ __('dpp.status_all') }}</a></li>
                                <li><a href="{{ route('dpp.request.show', [$playlist->id,'status'=>\App\Models\Entry::DPP_STATUS_PROCESSING]) }}">{{ __('dpp.status_processing') }}</a></li>
                                <li><a href="{{ route('dpp.request.show', [$playlist->id,'status'=>\App\Models\Entry::DPP_STATUS_REVIEW]) }}">{{ __('dpp.status_review') }}</a></li>
                                <li><a href="{{ route('dpp.request.show', [$playlist->id,'status'=>\App\Models\Entry::DPP_STATUS_PUBLISHED]) }}">{{ __('dpp.status_published') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content">
                <div class="playlist-list">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="playlist-title pt">{{ __('common.title') }}</th>
                            <th style="width: 100px;">{{ __('dpp.Duration') }}</th>
                            <th style="width: 100px;">{{ __('dpp.Scenes') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th class="text-right">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                    $status = [
                        \App\Models\Entry::DPP_STATUS_PROCESSING => 'label-grey',
                        \App\Models\Entry::DPP_STATUS_REVIEW => 'label-orange',
                        \App\Models\Entry::DPP_STATUS_PUBLISHED => 'label-active',
                    ];
                    @endphp
                    @foreach( $entries as $entry)
                        <tr>
                            <td class="playlist-title ptl"><a href="#">{{ $entry->name }}</a></td>
                            <td>{{ gmdate("i:s", $entry->duration) }}</td>
                            <td class="playlist-amount">
                                <span class="amount">{{ $entry->scenes_count }}</span> <small>{{ __('dpp.available') }}</small>
                            </td>
                            <td>
                                <span class="label  {{ $status[$entry->dpp_status] }}">{{ __('dpp.status_'.$entry->dpp_status) }}</span>
                            </td>
                            <td class="playlist-actions">
                                @if ($entry->dpp_status == \App\Models\Entry::DPP_STATUS_PROCESSING)
                                <a href="{{ route('dpp.request.scene.create', [$playlist->id, $entry->id]) }}" class="btn btn-normal btn-m">{{ __('common.manage') }}</a>
                                @if (optional($entry->platformAlivod)->source_url)
                                <a href="javascript:void(0);" onclick="showDownload(this)" download_url="{{ $entry->source_url }}" class="btn btn-normal btn-m">{{ __('common.download') }}</a>
                                @endif
                                @else
                                <a href="{{ route('dpp.request.scene.index', [$playlist->id, $entry->id]) }}" class="btn btn-normal btn-m">{{ __('common.view') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!-- MODAL -->
                <div class="modal fade" id="publish" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog s-width" role="document">
                        <div class="modal-content">
                         <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('dpp.publish_confirm') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <div>{!! __('dpp.publish_confirm_content', ['number'=>$newScenes]) !!}</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" onclick="publishPlaylist();" class="btn btn-secondary">{{ __('common.publish') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="incomplete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog s-width" role="document">
                        <div class="modal-content">
                         <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('dpp.publish_incomplete') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <div>{!! __('dpp.publish_incomplete_content') !!}</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">{{ __('common.close') }}</button>
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

                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info">
                            @if ($entries->count()>0)
                                {{
                                    __(
                                        'manage/cp/contents/playlists.showing_from_to_playlists',
                                        [
                                            'from'=>$entries->firstItem(),
                                            'to'=>$entries->lastItem(),
                                            'total'=>$entries->total()
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
                            {{ $entries->appends(request()->input())->links() }}
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

@push('header_scripts')
    <script>
        var udpateUrl = '{{ route('dpp.request.update', ['playlist_id' => $playlist->id]) }}';
        function publishPlaylist() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': window.params.csrfToken
                }
            });
            $.ajax({
                type: 'PUT',
                url: udpateUrl,
                data: {},
                dataType: 'json',
                success: function (data) {
                    window.location.href = '{{ route('dpp.request.show', ['playlist_id' => $playlist->id]) }}';
                },
                error: function (data) {
                }
            });
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
@endpush

