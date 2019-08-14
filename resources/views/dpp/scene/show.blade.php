@extends('partials.layout_dpp')

@push('title')
iVideoExchange | iVideoAdd Scenes
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-2">
            <div class="title-header">
                <a href="{{ route('dpp.request.scene.index', [$playlist_id, $entry_id]) }}" class="btn btn-normal btn-m">{{ __('dpp.back_to_manage_scenes') }}</a>
                <div class="title">{{ __('dpp.scene_detail') }}</div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('dpp.scene_information') }}</h5>
                </div>
                <div class="ibox-content">
                    <form method="get" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('dpp.scene_image') }}</label>
                            <div class="col-md-9"><div class="video-image"><img src="{{ \App\Services\Serve\ScenesImageService::getImageUrl($scene) }}"></div></div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('dpp.Scene') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $scene->name }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('dpp.scene_start') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("i:s", $scene->start_in_seconds) }}</div>
                            <label class="col-md-2 control-label">{{ __('dpp.scene_end') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("i:s", $scene->end_in_seconds) }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('dpp.scene_duration') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ ($scene->end_in_seconds - $scene->start_in_seconds). ' '. __('dpp.seconds') }}</div>
                            <label class="col-md-2 control-label">{{ __('dpp.scene_dpp_duration') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ $scene->dpp_duration. ' '. __('dpp.seconds') }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('dpp.scene_type') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ __('dpp.type_'.$scene->type) }}</div>
                            <label class="col-md-2 control-label">{{ __('dpp.scene_locale') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ $scene->locale }}</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
