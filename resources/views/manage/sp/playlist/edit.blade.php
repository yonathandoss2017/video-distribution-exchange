@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/playlist.page_title') }} | {{ __('app.title') }}
@endpush
@push('script-head')
<script src="/vendor/jquery/jquery.js"></script>
<script src="/vendor/jquery/jquery-ui.js"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    {{ Form::model($playlist, ['method'=>'PUT', 'url'=>route('manage.sp.playlist.update',[ $property_id, $playlist->id ]), 'class'=>'form-horizontal', 'files' => true]) }}
                    <div class="title-header ">
                        <a href="{{ route('manage.sp.playlists.index', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('manage/sp/content/playlist.back_to_playlists') }}</a>
                        <div class="title">{{ __('manage/sp/content/playlist.edit_playlist') }}</div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/sp/content/playlist.playlist_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/common.name') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" required="" name="playlist_name" id="playlist_name" value="{{ $playlist_name }}">
                                </div>
                            </div>

                            {!! \App\Services\FeaturedImageService::getHtml($playlist_thumbnail, \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id, \Carbon\Carbon::parse($playlist->published_at)->timestamp)) !!}
                            
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/content/playlist.tod') }}</label>
                                <div class="col-md-9">
                                    <span class="text-terms__value">
                                        @if(optional($playlist_tod)->name == 'Whitelist for own SP')
                                            {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                                        @else
                                            {{ optional($playlist_tod)->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-save">
                        <button type="submit" class="btn btn-primary">{{ __('manage/sp/common.save') }}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
