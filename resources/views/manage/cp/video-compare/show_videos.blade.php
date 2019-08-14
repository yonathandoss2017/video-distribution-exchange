<div class="video-list spwp">
    <table class="table">
        <thead>
        <tr>
            <th class="image"></th>
            <th class="video-name fx-video-name">{{ __('manage/cp/video-compare/compare.video_name') }}</th>
            <th>{{ __('manage/cp/video-compare/compare.similarity') }}</th>
            <th>{{ __('manage/cp/video-compare/compare.similar_situation') }}</th>
            <th style="width: 100px;">{{ __('manage/cp/video-compare/compare.total_duration') }}</th>
            <th class="text-right"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($compareResults as $compareResult)
            <tr>
                <td class="image">
                    <div class="video-img">
                        @if($compareResult->entry)
                            <img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($compareResult->entry, $compareResult->entry->property_id, null, 120) }}" alt="">
                        @else
                            <img src="{{ url('/images/video-default.jpg') }}" alt="">
                        @endif
                    </div>
                </td>
                <td class="video-name fx-video-name">
                    <a href="javascript:void(0)">{{ optional($compareResult->entry)->name }}</a>
                </td>
                <td class="playlist-amount">
                    <span class="amount">{{ $compareResult->confidence }}</span>
                </td>
                <td>{{ $compareResult->distortion }}</td>
                <td class="duration">{{ $compareResult->length_ms / 1000 }} {{ __('manage/cp/video-compare/compare.second') }}</td>
                <td><i class="fa fa-angle-down fa-2x pull-right fa-angle" onclick="showToggle(this)" style="margin-top: 0px;" aria-hidden="true"></i></td>
            </tr>
            <tr class="fragments hidden">
                <td colspan="6" style="padding-top: 0px;">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="right" style="width: 10%;">#</th>
                            <th>{{ __('manage/cp/video-compare/compare.search_video_start_position') }}</th>
                            <th>{{ __('manage/cp/video-compare/compare.match_video_start_position') }}</th>
                            <th class="right" style="width: 10%;">{{ __('manage/cp/video-compare/compare.duration') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($compareResult->matchFragments as $index => $fragment)
                            <tr>
                                <td class="right" style="width: 10%;">{{ $index + 1 }}</td>
                                <td>
                                    @if ($fragment->searchedVideoStartingMatchedPosition_ms >= 3600000)
                                        {{gmdate('H:i:s', $fragment->searchedVideoStartingMatchedPosition_ms / 1000)}}
                                    @elseif ($fragment->searchedVideoStartingMatchedPosition_ms > 0)
                                        {{gmdate('i:s', $fragment->searchedVideoStartingMatchedPosition_ms / 1000)}}
                                    @else
                                        00:00
                                    @endif
                                </td>
                                <td>
                                    @if ($fragment->matchedVideoStartingMatchedPosition_ms >= 3600000)
                                        {{gmdate('H:i:s', $fragment->matchedVideoStartingMatchedPosition_ms / 1000)}}
                                    @elseif ($fragment->matchedVideoStartingMatchedPosition_ms > 0)
                                        {{gmdate('i:s', $fragment->matchedVideoStartingMatchedPosition_ms / 1000)}}
                                    @else
                                        00:00
                                    @endif
                                </td>
                                <td class="right" style="width: 10%;"><span class="amount">{{ $fragment->duration_ms / 1000 }}</span> <small>{{ __('manage/cp/video-compare/compare.second') }}</small></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info">
                @if ($compareResults->count() > 0)
                    {{
                        __(
                            'manage/cp/video-compare/compare.showing_from_to_videos',
                            [
                                'from'=>$compareResults->firstItem(),
                                'to'=>$compareResults->lastItem(),
                                'total'=>$compareResults->total()
                            ]
                        )
                    }}
                @else
                    {{ __('manage/cp/video-compare/compare.no_videos') }}
                @endif
            </div>
        </div>

        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers">
                {{ $compareResults->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>