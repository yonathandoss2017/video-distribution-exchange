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
            <a href="{{ route('dpp.request.scene.create', [$playlist_id, $entry_id]) }}" class="btn btn-normal btn-m">{{ __('dpp.back_to_manage_scenes') }}</a>
            <div class="title">{{ __('dpp.edit_scene') }}</div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ __('manage/cp/ivideoadd/ivideoadd.scene_information') }}</h5>
            </div>
            {{ Form::open(['url' => route('dpp.request.scene.update', [$playlist_id,$entry_id,$scene->id]), 'method'=>'PUT', 'class'=>'form-horizontal', 'id'=>'form_scene']) }}
            <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.scene_image') }}</label>
                        <div class="col-md-9"><div class="video-image"><img src="{{ \App\Services\FeaturedImageService::generateImageUrl($scene->image_path) }}"></div></div>
                    </div>

                    <div class="form-group row {{ $errors->has('name') ? " has-danger" : "" }}">
                        <label for="name" class="col-md-3 control-label">{{ __('dpp.scene_name').'*' }}</label>
                        <div class="col-md-9">
                            <input class="form-control" required="true" name="name" type="text" id="name" value="{{ old('name')?old('name'):$scene->name }}">
                            @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                        </div>
                    </div>

                    <div class="form-group row {{ $errors->has('start')||$errors->has('end')||$errors->has('end_in_seconds') ? " has-danger" : "" }}">
                        <label for="start" class="col-md-3 control-label">{{ __('dpp.scene_start').'*' }}</label>
                        <div class="col-md-2 scene-detail">
                            {{ Form::hidden('start_in_seconds', old('start_in_seconds')??$scene->start_in_seconds??0) }}
                            <input class="form-control scene-time" required="true" name="start" type="text" id="start" pattern="^[0-9]{2}:[0-9]{2}:[0-9]{2}$" placeholder="HH:MM:SS" value="{{ old('start')?old('start'):gmdate("H:i:s", $scene->start_in_seconds) }}">
                            @include('partials.errors', ['err_type' => 'field','field' => 'start'])
                        </div>
                        <label for="end" class="col-md-3 control-label">{{ __('dpp.scene_end').'*' }}</label>
                        <div class="col-md-2 scene-detail">
                            {{ Form::hidden('end_in_seconds', old('end_in_seconds')??$scene->end_in_seconds??0) }}
                            <input class="form-control scene-time" required="true" name="end" type="text" id="end" pattern="^[0-9]{2}:[0-9]{2}:[0-9]{2}$" value="{{ old('end')?old('end'):gmdate("H:i:s", $scene->end_in_seconds) }}">
                            @include('partials.errors', ['err_type' => 'field','field' => 'end'])
                            @include('partials.errors', ['err_type' => 'field','field' => 'end_in_seconds'])
                        </div>
                    </div>

                    <div class="form-group row {{ $errors->has('dpp_duration') ? " has-danger" : "" }}">
                        <label class="col-md-3 control-label">{{ __('dpp.scene_duration') }}</label>
                        <div id="scene_duration" class="col-md-2 scene-detail control-label t-a-l">{{ gmdate("H:i:s", ($scene->end_in_seconds-$scene->start_in_seconds)) }}</div>
                        <label for="dpp_duration" class="col-md-3 control-label">{{ __('dpp.scene_dpp_duration') }}*</label>
                        <div class="col-md-2 scene-detail" >
                            <input class="form-control" required="true" name="dpp_duration" type="text" id="dpp_duration" pattern="^[0-9]{2}:[0-9]{2}:[0-9]{2}$" value="{{ old('dpp_duration')?old('dpp_duration'):gmdate("H:i:s", $scene->dpp_duration) }}">
                            @include('partials.errors', ['err_type' => 'field','field' => 'dpp_duration'])
                        </div>
                    </div>


                    <div class="form-group row {{ $errors->has('type') ? " has-danger" : "" }}">
                        <label class="col-md-3 control-label">{{ __('dpp.scene_type').'*' }}</label>
                        <div class="col-md-9">
                            <select class="form-control" name="type">
                                <option value="" {{ (old('type')?old('type'):$scene->type)?'':'selected' }} required="true">{{ __('dpp.scene_type_select') }}</option>
                                @foreach( \App\Models\EntryScene::getTypes() as $type)
                                    <option value="{{ $type }}" {{ $type == (old('type')?old('type'):$scene->type)?'selected':'' }}>{{ __('dpp.scene_type_'.$type) }}</option>
                                @endforeach
                            </select>
                            @include('partials.errors', ['err_type' => 'field','field' => 'type'])
                        </div>
                    </div>

                    <div class="form-group row {{ $errors->has('locale') ? " has-danger" : "" }}">
                        <label class="col-md-3 control-label">{{ __('dpp.scene_locale').'*' }}</label>
                        <div class="col-md-9">
                            <input class="form-control" required="true" name="locale" type="text" id="" value="{{ old('locale')?old('locale'):$scene->locale }}">
                            @include('partials.errors', ['err_type' => 'field','field' => 'locale'])
                        </div>
                    </div>
            </div>
        </div>
        <div class="form-save">
            <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@push('js')
<script src="/js/dpp/masked_input_1.4-min.js"></script>
<script>
    function formatSecondsAsTime(secs) {
        var hr = Math.floor(secs / 3600);
        var min = Math.floor((secs - (hr * 3600)) / 60);
        var sec = Math.floor(secs - (hr * 3600) - (min * 60));

        if (hr < 10) {
            hr = "0" + hr;
        }
        if (min < 10) {
            min = "0" + min;
        }
        if (sec < 10) {
            sec = "0" + sec;
        }

        return hr + ':' + min + ':' + sec;
    }

    function cbTimeChange (mask, index) {
        var startTime = $('#start').val().split(':');
        var endTime = $('#end').val().split(':');
        var startSeconds = (+startTime[0]) * 60 * 60 + (+startTime[1]) * 60 + (+startTime[2]);
        var endSeconds = (+endTime[0]) * 60 * 60 + (+endTime[1]) * 60 + (+endTime[2]);

        $("input[name='start_in_seconds']").val(startSeconds);
        $("input[name='end_in_seconds']").val(endSeconds);
        // minutes are worth 60 seconds. Hours are worth 60 minutes.
        var secs = endSeconds - startSeconds;
        if (secs > 0) {
            var duration = formatSecondsAsTime(secs);
            $('#scene_duration').text(duration);
        } else {
            $('#scene_duration').text('00:00:00');
        }
    }

    MaskedInput({
        elm: document.getElementById('start'),
        format: '__:__:__',
        separator: ':',
        onfilled: cbTimeChange
    });

    MaskedInput({
        elm: document.getElementById('end'),
        format: '__:__:__',
        separator: ':',
        onfilled: cbTimeChange
    });

    MaskedInput({
        elm: document.getElementById('dpp_duration'),
        format: '__:__:__',
        separator: ':'
    });
</script>
@endpush
