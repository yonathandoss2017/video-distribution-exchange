@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }} | {{ __('manage/cp/ivideoadd/ivideoadd.new_dpp') }}
@endpush

@php
    $jquery_in_head = true;
@endphp

@push('script-head')
    <script src="/vendor/jquery/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <style type="text/css">
        .playlist-name {
            height: 32px;
            line-height: 32px;
        }
        .select2-selection {
            border-radius: 0 !important;
            min-height: 34px;
            padding-left: 8px;
            padding-top: 2px;
            border: 1px solid #e5e6e7;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 offset-md-3 text-center hidden" id="error-area">
            <div class="alert alert-danger">
                <strong>{{ trans('signup.oops') }}</strong>
                <span id="error-text">{{ __('manage/cp/ivideoadd/ivideoadd.something_went_wrong') }}</span>
            </div>
        </div>
    </div>
</div>
<!-- Begin page content -->
<div class="container">

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="title-header ">
                <a href="{{ route('manage.cp.dpp.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/ivideoadd/ivideoadd.back_to_ivideoadd') }}</a>
                <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd.new_dpp') }}</div>
            </div>
            <div class="form">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/ivideoadd/ivideoadd.playlist_information') }}</h5>
                    </div>
                    <div class="ibox-content">

                        {!! Form::open([
                                'method'=>'POST',
                                'url' => route('manage.cp.dpp.videos.select', [ $property_id]),
                                'class'=>'form-horizontal',
                                'id' => 'review-form',
                            ]) !!}
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.playlist') }}</label>
                                @if (isset($playlist))
                                    <input type="hidden" name="playlists" id="playlists" value="{{ $playlist->id }}">
                                    <div class="col-md-9 playlist-name">{{ $playlist->name }}</div>
                                @else
                                    <div class="col-md-9">
                                        {{ Form::select('playlists',
                                        $playlists,
                                         null,
                                         [
                                         'id' => 'playlists',
                                         'class' => 'selectpicker form-control',
                                         'placeholder' => __('manage/cp/ivideoadd/ivideoadd.select_a_playlist'),
                                         'required' => "true",
                                         'onchange' => 'hideError();',
                                         ]) }}
                                    </div>
                                @endif
                            </div>

                            <input type="hidden" name="selectedList" id="selectedList" value="">
                        {{ Form::close() }}

                    </div>
                </div>
            </div>
            <div class="form-save">
            <button type="submit" class="btn btn-primary" data-dismiss="modal" aria-label="Close" id="view-videos" onclick="viewVideos()">{{ __('manage/cp/ivideoadd/ivideoadd.view_videos') }}</button>
            </div>
        </div>

        <div class="col-md-8 offset-md-2 m-t-2 hidden" id="video-select-area">
            <div class="title-header ">
                <div class="min-menu row">
                    <div class="col-md-5">
                        <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd.playlist_videos') }}</div>
                    </div>
                    <div class="col-md-7">
                        <div class="right-panel video-selected">
                          <span class="amount">0</span> {{ __('manage/cp/ivideoadd/ivideoadd.video_selected') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox" id="video-list">
                {{-- Show video list by ajax --}}
            </div>
            <div class="form-save"><button type="submit" class="btn btn-primary" onclick="reviewVideos();">{{ __('manage/cp/ivideoadd/ivideoadd.review') }}</button></div>
        </div>

        <!-- MODAL -->
        <div id="modal"></div>

    </div>
</div>
@stop

@push('js')
<script>
    var baseGetVideoUrl = '{{ url("manage/$property_id/cp/dpp/") }}';
    var reviewUrl = '{{ route('manage.cp.dpp.create.review', $property_id) }}';
    var errorTexts = {
        'select_a_playlist': '{{ __('manage/cp/ivideoadd/ivideoadd.please_select_playlist') }}',
        'please_select_videos': '{{ __('manage/cp/ivideoadd/ivideoadd.please_select_videos') }}',
        'general_error': '{{ __('manage/cp/ivideoadd/ivideoadd.general_error') }}',
    };
    var hasPlaylistId = '{{ isset($playlist) }}';
</script>
<script type="text/javascript" src="{{ asset('js/dpp/create.js') }}"></script>
<script type="text/javascript" src="{!! asset('js/videoplayer/videoplayer.js') !!}"></script>
@endpush

