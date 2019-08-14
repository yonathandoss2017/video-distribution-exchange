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
                        <div class="col-md-9"><div class="video-image"><img src="{{ \App\Services\FeaturedImageService::generateImageUrl($scene->image_path) }}"></div></div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene') }}</label>
                        <div class="col-md-9 control-label t-a-l">{{ $scene->name }}</div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_start') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("i:s", $scene->start_in_seconds) }}</div>
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_end') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("i:s", $scene->end_in_seconds) }}</div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_duration') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ ($scene->end_in_seconds - $scene->start_in_seconds). ' '. __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }}</div>
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.dpp_duration') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ $scene->dpp_duration. ' '. __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }}</div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_'.$scene->type) }}</div>
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_locale') }}</label>
                        <div class="col-md-2 scene-detail control-label t-a-l">{{ $scene->locale }}</div>
                    </div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_configuration') }}</h5>
            </div>
            <div class="ibox-content">
                {{ Form::open(['url' => route('manage.cp.dpp.scenes.update', [$property_id,$playlist_id,$entry_id,$scene->id]), 'method'=>'PUT', 'class'=>'form-horizontal', 'id'=>'form_scene']) }}
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_suitable') }}</label>
                        <div class="col-md-9">
                            <input class="form-control" name="suitable_verticals" type="text" id="" value="{{ $scene->suitable }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_blacklist') }}</label>
                        <div class="col-md-9">
                            <input class="form-control" name="blacklisted_verticals" type="text" id="" value="{{ $scene->blacklist }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.descriptions') }}</label>
                        <div class="col-md-9">
                            <textarea class="form-control-area" id="description" rows="3" name="description" tabindex="2">{{ $scene->description }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('common.keywords') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="keywords" value="{{ $scene->keywords }}" tabindex="3">
                            <div class="description">{{ __('manage/cp/ivideoadd/ivideoadd_scene.keywords_hint') }}</div>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="form-save">
            <button type="submit" class="btn btn-primary" onclick="$('#form_scene').submit()">{{ __('common.save') }}</button>
        </div>
        </div>
    </div>
</div>
@stop
