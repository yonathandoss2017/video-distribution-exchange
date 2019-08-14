@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.request-logs.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/request_logs.back_to_request_logs') }}</a>
                        <div class="title">{{ $playlist->name }}</div>
                    </div>
                    <form method="" class="form-horizontal">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/playlists.playlist_information') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('common.name') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->name }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.genre') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->genre }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.region') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->region }}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.language') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->language }}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('common.cast') }}</label>
                                    <div class="col-md-9  control-label t-a-l">{{ $playlist->stars }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/request_logs.featured_image') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/request_logs.featured_image') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        <div class="creative-image">
                                            <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id) }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Comments</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form form-horizontal">
                                    <div class="form-group row">
                                        <div class="col-md-12 control-label t-a-l">{{ empty($playlist->comment) ? '-' : $playlist->comment }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                    @if (!$isContentUploader) 
                        <div class="form-save">
                            <form method="post" action="{{ route('manage.cp.request-logs.reject', [$property_id, $playlist->id]) }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="Playlist">
                                <button type="submit" class="btn btn-primary btn-m float-right">{{ __('manage/cp/contents/request_logs.reject') }}</button>
                            </form>
                            <form method="post" action="{{ route('manage.cp.request-logs.approve', [$property_id, $playlist->id]) }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="Playlist">
                                <button type="submit" class="btn btn-primary btn-m float-right m-r">{{ __('manage/cp/contents/request_logs.approve') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop