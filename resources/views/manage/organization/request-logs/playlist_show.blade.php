@extends('partials.layout_home')

@push('title')
    {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.organization.request-logs.index') }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/request_logs.back_to_request_logs') }}</a>
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
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.marketplace') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        @if (1 == $playlist->publish) 
                                            {{ __('manage/cp/contents/playlists.publish_to_marketplace') }}
                                        @else
                                            {{ __('manage/cp/contents/playlists.don\'t_Publish') }}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.marketplace_terms') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->marketplaceTerm ? $playlist->marketplaceTerm->name : '' }}</div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.start_date') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->publish_start_date ? $playlist->publish_start_date : __('manage/cp/contents/playlists.available_now') }}</div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/playlists.end_date') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $playlist->publish_end_date ? $playlist->publish_end_date : __('manage/cp/contents/playlists.until_forever') }}</div>
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
                                        <div class="col-md-12 control-label t-a-l">{{ empty($playlist->publish_review_comment) ? '-' : $playlist->publish_review_comment }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="form-save">
                        <form method="post" action="{{ route('manage.organization.request-logs.reject', $playlist->id) }}">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-primary btn-m float-right">{{ __('manage/cp/contents/request_logs.reject') }}</button>
                        </form>
                        <form method="post" action="{{ route('manage.organization.request-logs.approve', $playlist->id) }}">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-primary btn-m float-right m-r">{{ __('manage/cp/contents/request_logs.approve') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop