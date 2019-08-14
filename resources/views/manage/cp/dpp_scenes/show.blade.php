@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_name', ['name'=>$scene->name]) }}
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-2">
            <div class="title-header">
                <a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/ivideoadd/ivideoadd_scene.back_to_dpp_video') }}</a>
                <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_name', ['name'=>$scene->name]) }}</div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_information') }}</h5>
                </div>
                <div class="ibox-content">
                    <form method="get" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_image') }}</label>
                            <div class="col-md-9"><div class="video-image"><img src="{{ \App\Services\Serve\ScenesImageService::getImageUrl($scene) }}"></div></div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $scene->name }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_start') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("H:i:s", $scene->start_in_seconds) }}</div>
                            <label class="col-md-2 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_end') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("H:i:s", $scene->end_in_seconds) }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_duration') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ ($scene->end_in_seconds - $scene->start_in_seconds). ' '. __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }}</div>
                            <label class="col-md-2 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.dpp_duration') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("H:i:s", $scene->dpp_duration) }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ isset($scene->type) ? ucfirst($scene->type) : 'N/A' }}</div>
                            <label class="col-md-2 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_locale') }}</label>
                            <div class="col-md-2 scene-detail control-label t-a-l">{{ isset($scene->locale) ? $scene->locale : 'N/A' }}</div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_configuration') }}</h5>
                </div>
                <div class="ibox-content">
                    <form method="get" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_suitable') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $scene->suitable }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_blacklist') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $scene->blacklist }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.descriptions') }}</label>
                            <div class="col-md-9 control-area t-a-l">{{ $scene->description }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('common.keywords') }}</label>
                            <div class="col-md-9 control-label t-a-l">{{ $scene->keywords }}</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
