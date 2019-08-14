@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/exchange/request_log.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form-horizontal">
                    <div class="title-header">
                        <div class="min-menu row">
                            <div class="col-md-3">
                                <div class="title">{{ __('manage/sp/exchange/request_log.request_details') }}</div>
                            </div>
                            <div class="col-md-9">
                                <div class="right-panel">
                                    <a href="{{ route('manage.sp.request-log.index', $property) }}" class="btn btn-normal btn-m">
                                        {{ __('manage/sp/exchange/request_log.back_to_request_logs') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.title-header -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/sp/exchange/request_log.service_provider_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/exchange/request_log.requested_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    {{ $requestLog->user->name }} &lt;{{ $requestLog->user->email }}&gt;
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/exchange/request_log.subject') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    {{ $requestLog->subject }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/exchange/request_log.message') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    {{ $requestLog->message }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.ibox -->
                </div> <!-- /.form-horizontal -->

                <p>&nbsp;</p>

                <div class="form-horizontal">
                    <div class="title-header">
                        <div class="title">{{ __('manage/sp/exchange/request_log.playlists_requested') }}</div>
                    </div> <!-- /.title-header -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ $requestLog->playlists_count }} {{ __('manage/sp/exchange/request_log.playlists_requested') }}</h5>
                        </div>
                        <div class="ibox-content pt-0">
                            <div class="video-list spwp">
                                <table class="table">
                                    <tbody>
                                    @foreach($requestLog->playlists as $playlist)
                                    <tr>
                                        <td class="image-small">
                                            <div class="video-img video-img-small">
                                                <a href="#">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id, \Carbon\Carbon::parse($playlist->published_at)->timestamp) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="playlist-title">
                                            <a href="#">{{ $playlist->name }}</a> <br/>
                                            <small><b>{{ $playlist->entries_count }}</b> {{ __('manage/sp/common.videos') }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /.row -->
    </div> <!-- /.container -->

@stop
@push('js')
@endpush
