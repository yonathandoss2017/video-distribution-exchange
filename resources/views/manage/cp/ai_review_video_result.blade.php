<div class="video-list spwp">
    <table class="table">
        <thead>
        <tr>
            <th class="playlist-title ptl"></th>
            <th>{{ __('manage/cp/contents/videos.ai_porn_label') }}</th>
            <th>{{ __('manage/cp/contents/videos.ai_terrorism_label') }}</th>
            <th class="playlist-actions">{{ __('manage/cp/contents/videos.ai_timestamp') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($aiReviewVideoResults as $aiReviewVideoResult)
        <tr>
            <td class="image-small">
                <div class="video-img video-img-small">
                    <a href="javascript:void(0)">
                        <img class="ai-result-image" onclick="showImage(this)" src="{{ \App\Services\Serve\AiReviewImageService::getImageUrl($aiReviewVideoResult, null, 300) }}">
                    </a>
                </div>
            </td>
            <td class="">
                <span>@if($aiReviewVideoResult->porn_label){{ __('manage/cp/contents/request_logs.ai_label_'.$aiReviewVideoResult->porn_label) }}@else - @endif</span>
            </td>
            <td class="">
                <span>@if($aiReviewVideoResult->terrorism_label){{ __('manage/cp/contents/request_logs.ai_label_'.$aiReviewVideoResult->terrorism_label) }}@else - @endif</span>
            </td>
            <td class="playlist-actions">
                @if ($aiReviewVideoResult->timestamp / 1000 >= 3600)
                    {{gmdate('H:i:s', $aiReviewVideoResult->timestamp / 1000)}}
                @else
                    {{gmdate('i:s', $aiReviewVideoResult->timestamp / 1000)}}
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-5">
            <div class="dataTables_info">
                @if ($aiReviewVideoResults->count()>0)
                    {{
                        __(
                            'manage/cp/contents/request_logs.showing_from_to_ai_review_results',
                            [
                                'from'=>$aiReviewVideoResults->firstItem(),
                                'to'=>$aiReviewVideoResults->lastItem(),
                                'total'=>$aiReviewVideoResults->total()
                            ]
                        )
                    }}
                @endif
            </div>
        </div>
        <div class="col-md-7">
            <div class="dataTables_paginate paging_simple_numbers">
                {{ $aiReviewVideoResults->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
