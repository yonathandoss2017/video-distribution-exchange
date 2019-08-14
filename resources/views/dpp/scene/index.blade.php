@extends('partials.layout_dpp')

@push('title')
iVideoExchange | iVideoAdd Scenes
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header ">
                <a href="{{ route('dpp.request.show', $playlist_id) }}" class="btn btn-normal btn-m">{{ __('dpp.back_to_dpp_video') }}</a>
                <div class="title">{{ __('dpp.manage_scenes').$entry->name }}</div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="ibox dpp-upload-scenes">
                        <div class="ibox-content">
                            <div class="playlist-list">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="playlist-title ptl">{{ __('dpp.scene_type') }}</th>
                                            <th>{{ __('dpp.total_scenes') }}</th>
                                            <th>{{ __('dpp.total_dpp_time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $scene_summary as $scene)
                                        <tr>
                                            <td class="playlist-title ptl">
                                                <a href="#">{{ __('dpp.scene_type_'.$scene['type']) }}</a>
                                            </td>
                                            <td class="playlist-amount">
                                                <span class="amount">{{ $scene['scenes_num'] }}</span> <small>{{ __('dpp.scenes') }}</small>
                                            </td>
                                            <td class="right"><span class="amount">{{ $scene['dpp_sum'] }}</span> <small>{{ __('dpp.seconds') }}</small></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row platforms row-eq-height">
                @foreach( $entry->scenes as $scene)
                    <div class="col-md-2 scenes-m">
                        <div class="video-img main">
                            <a href="javascript:void(0)"><img src="{{ \App\Services\Serve\ScenesImageService::getImageUrl($scene) }}" alt=""></a>
                        </div>
                        <div class="info">
                            <div class="video-title">{{ $scene->name }}</div>
                            <div class="small-detail m-t-sm">
                                <a href="{{ route('dpp.request.scene.show', [$playlist_id, $entry->id, $scene->id]) }}" class="btn btn-normal btn-m no-float">{{ __('common.view') }}</a>
                            </div>
                            <div class="clearfix"> </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="m-t">{{ __('dpp.total_scenes_uploaded', ['scenes'=>$entry->scenes->count()]) }}</div>
        </div>
    </div>
</div>
@stop
