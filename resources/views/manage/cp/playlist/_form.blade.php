@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/contents/playlists.playlist_information') }}
@endpush
@push('script-head')
    <script src="/vendor/jquery/jquery.js"></script>
@endpush

<div class="ibox">
    <div class="ibox-title">
        <h5>{{ __('manage/cp/contents/playlists.playlist_information') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="form-group row {{ $errors->has('name') ? " has-danger" : "" }}">
            <label class="col-md-3 control-label">{{ __('common.name') }}*</label>
            <div class="col-md-9">
                {!! Form::text('name', null, ['id' => 'name', 'class' => 'form-control', 'placeholder' => __('common.name'), 'required']) !!}
                @include('partials.errors', ['err_type' => 'field','field' => 'name'])
            </div>
        </div>

        @if (!empty($playlist))
            {!! \App\Services\FeaturedImageService::getHtml($playlist->thumbnail_path, \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $playlist->property_id)) !!}
        @else
            {!! \App\Services\FeaturedImageService::getHtml() !!}
        @endif

        <div class="form-group row {{ $errors->has('genre') ? " has-danger" : "" }}">
            <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.genre') }}*</label>
            <div class="col-md-9">
                {{ Form::select('genre', $genres, null, ['class' => 'form-control', 'placeholder' => __('manage/cp/contents/playlists.select_a_genre'), 'required']) }}
                @include('partials.errors', ['err_type' => 'field','field' => 'genre'])
            </div>
        </div>

        <div class="form-group row {{ $errors->has('region') ? " has-danger" : "" }}">
            <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.region') }}*</label>
            <div class="col-md-9">
                {{ Form::select('region', $countries, null, ['class' => 'form-control', 'placeholder' => __('manage/cp/contents/playlists.select_a_country'), 'required']) }}
                @include('partials.errors', ['err_type' => 'field','field' => 'region'])
            </div>
        </div>

        <div class="form-group row {{ $errors->has('language') ? " has-danger" : "" }}">
            <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.language') }}*</label>
            <div class="col-md-9">
                {{ Form::select('language', $languages, null, ['class' => 'form-control', 'placeholder' => __('manage/cp/contents/playlists.select_a_language'), 'required']) }}
                @include('partials.errors', ['err_type' => 'field','field' => 'language'])
            </div>
        </div>

    </div>
</div>
<input name="is_submit" type="hidden" value="">

@if (config('features.content_review'))
    <div class="ibox">
        <div class="ibox-title">
            <h5>{{ __('manage/cp/contents/playlists.review_comments') }}</h5>
        </div>
        <div class="ibox-content">
            <div class="form form-horizontal">
                <div class="form-group row">
                    <div class="col-md-12 control-label t-a-l">{{ empty($playlist->comment) ? '-' : $playlist->comment }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="form-save">
    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
    @if(config('features.content_review'))
        <button type="button" class="btn btn-primary" onclick="submitForm()">{{ __('common.save_and_submit') }}</button>
    @endif
</div>

@push('js')
    <script>
        function submitForm() {
            $("input[name='is_submit']").val(true)
            $( "#playlist_form" ).submit();
        }
    </script>
@endpush
