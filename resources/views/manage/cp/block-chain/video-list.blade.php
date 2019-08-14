
<div class="ibox-content">
    <div class="video-list dpp-video">
        <table class="table" id="video-list-table">
            <thead>
                <tr>
                    <th>
                        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="chkCheckAll" onclick="checkAll(this);"><span class="custom-control-indicator"></span></label>
                    </th>
                    <th class="image"></th>
                    <th class="video-name fx-video-name">{{ __('manage/cp/block-chain/block-chain.title') }}</th>
                    <th class="duration">{{ __('manage/cp/block-chain/block-chain.duration') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                <tr>
                    <td>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input chk-item" id="chkItem" data-id="{{ $video->id }}" onclick="selectItem(this);"><span class="custom-control-indicator"></span></label>
                    </td>
                    <td class="image">
                        <div class="video-img">
                            <a href="#"><img src="{{ App\Services\Serve\VideoImageService::getImageUrl($video, $property_id, null, 240) }}"></a>
                        </div>
                    </td>

                    <td class="video-name fx-video-name">
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $video -> id }}" onclick="getPlayer('{{ $video->id }}')">{{ $video->name }}</a>
                    </td>
                    <td class="duration">
                        @if ($video->duration >= 3600)
                            {{ gmdate('H:i:s', $video->duration) }}
                        @else
                            {{ gmdate('i:s', $video->duration) }}
                        @endif
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

