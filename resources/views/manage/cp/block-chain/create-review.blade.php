@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/block-chain/block-chain.review_evidence_videos') }} — {{ $playlist->name }}
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="title-header ">
                        <div class="title">{{ __('manage/cp/block-chain/block-chain.review_evidence_videos') }} — {{ $playlist->name }}</div>
                    </div>
                    <div class="ibox">
                    <div class="ibox-content">
                        <div class="video-list dpp-video">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="image"></th>
                                        <th class="video-name fx-video-name">{{ __('manage/cp/block-chain/block-chain.title') }}</th>
                                        <th class="duration">{{ __('manage/cp/block-chain/block-chain.duration') }}</th>
                                        <th width="100">{{ __('manage/cp/block-chain/block-chain.entity') }}</th>
                                        <th class="text-right">{{ __('manage/cp/block-chain/block-chain.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($videos as $video)
                                    <tr>
                                        <td class="image">
                                            <div class="video-img">
                                                <a href="#"><img src="{{ App\Services\Serve\VideoImageService::getImageUrl($video, $property_id, null, 240) }}"></a>
                                            </div>
                                        </td>
                                        <td class="video-name fx-video-name">
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video->id }}" onclick="getPlayer('{{ $video->id }}')">{{ $video->name }}</a>
                                        </td>
                                        <td class="duration">
                                            @if ($video->duration >= 3600)
                                                {{ gmdate('H:i:s', $video->duration) }}
                                            @else
                                                {{ gmdate('i:s', $video->duration) }}
                                            @endif
                                        </td>
                                        <td width="100">{{ $entity }}</td>
                                        <td>
                                            <a class="btn btn-normal btn-m delete" data-id="{{ $video->id }}" onclick="showConfirmDelete(this);"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="row">

                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($videos->count()>0)
                                        {{
                                            __(
                                                'manage/cp/block-chain/block-chain.video_pagination',
                                                [
                                                    'from'=>$videos->firstItem(),
                                                    'to'=>$videos->lastItem(),
                                                    'total'=>$videos->total()
                                                ]
                                            )
                                        }}
                                        @else
                                            {{ __('manage/cp/block-chain/block-chain.no_videos') }}
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
                @if (count($videoIds) > 0)
                <div class="form-save"><button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#submit">{{ __('manage/cp/block-chain/block-chain.submit_for_evidence') }}</button></div>
                @endif
            </div>
        </div>
        <div class="modal fade" id="submit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                 <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/block-chain/block-chain.submit_for_evidence') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <div>{{ __('manage/cp/block-chain/block-chain.enable_evidence_for_your_videos') }}</div>
                    </div>
                    <div class="modal-footer">
                        {!! Form::open([
                                'method'=>'POST',
                                'url' => route('manage.cp.block-chain.store', [$property_id]),
                                'class'=>'form-horizontal',
                                'id' => 'add-form',
                            ]) !!}
                            <input type="hidden" name="selectedList" id="selectedList" value="{{ implode(',', $videoIds) }}">
                            <input type="hidden" name="playlistId" id="playlistId" value="{{ $playlistId }}">
                            <input type="hidden" name="entity" id="entity" value="{{ $entity }}">
                            <button type="submit" class="btn btn-secondary">{{ __('manage/cp/block-chain/block-chain.submit') }}</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                 <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/block-chain/block-chain.confirm_remove_video') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <div>{{ __('manage/cp/block-chain/block-chain.do_you_want_to_remove_this_video') }}?</div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="video-delete-id" id="video-delete-id">
                        <button type="submit" class="btn btn-secondary" onclick="deleteVideo();">{{ __('manage/cp/block-chain/block-chain.delete') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <div id="modal"></div>

</div>
@stop

@push('js')
<script>
    var playlistId = '{{ $playlistId }}';
    var reviewUrl = '{{ route('manage.cp.block-chain.create.review', $property_id) }}';
    var selectedVideoList = [
        @foreach ($videoIds as $videoId)
            '{{ $videoId }}',
        @endforeach
    ];
    var errorTexts = {
        'general_error': '{{ __('manage/cp/block-chain/block-chain.general_error') }}',
    };

    function showConfirmDelete(element) {
      var videoId = $(element).data('id');
      $('#video-delete-id').val(videoId);
      $('#confirmModal').modal('show');
    }

    function deleteVideo() {
      var videoId = $('#video-delete-id').val();
      reviewUrl += '?deleteVideoId=' + videoId;
      window.location.href = encodeURI(reviewUrl);
    }

    // Find a item in source array
    function findItemInArray(arrSource, itemValue) {
      var index = -1;
      for (var i = arrSource.length - 1; i >= 0; i--) {
        if (arrSource[i] == itemValue) {
          return i;
        }
      }
      return index;
    }
</script>
<script type="text/javascript" src="{!! asset('js/videoplayer/videoplayer.js') !!}"></script>
@endpush
