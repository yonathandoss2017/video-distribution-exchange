@extends('admin.layout')
@push('title') {{ __('admin/sidebar.service_providers') }} | {{ __('app.title') }} @endpush
@section('content')

        <div class="container">

            <div class="row justify-content-center">

                <header class="title-header col-md-9">
                    <h3 class="title">{{ $property->name }}</h3>
                </header>

                <div class="col-md-9">
                    <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.service_providers') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/common.api') }}</strong></p>
                </div><!-- .col-* -->

                <div class="col-md-9">

                    <div class="ibox">

                        <div class="ibox-title">
                            <h5>{{ __('admin/service_provider.service_provider_common') }}</h5>
                        </div>

                        <div class="ibox-content">

                            <form class="form-horizontal">

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('admin/service_provider.sps') }} / {{ __('admin/service_provider.external_api_request') }}</label>
                                    <div class="col-md-9 control-label t-a-l link-text">
                                        @if ($entry)
                                            <a href="{{ route('api.v2.common.entry', ['entry_id' => $entry->id, 'key' => $property->api_key, 'token' => $property->api_token]) }}" target="_blank">{{ route('api.v2.common.entry', ['entry_id' => $entry->id, 'key' => $property->api_key, 'token' => $property->api_token]) }}</a>
                                        @else
                                            <span>{{ __('admin/service_provider.no_entry') }}</span>
                                        @endif
                                    </div>
                                </div>

                            </form>

                        </div><!-- .ibox-content -->

                    </div><!-- .ibox -->

                        <div class="ibox">

                            <div class="ibox-title">
                                <h5>{{ __('admin/service_provider.ivideomobile') }}</h5>
                            </div>

                            <div class="ibox-content">

                                <form class="form-horizontal">

                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('admin/service_provider.all_playlists_api') }}</label>
                                        <div class="col-md-9 control-label t-a-l link-text">
                                            <a href="{{ route('api.v1.sp-mobile.playlist', ['per_page' => 5, 'page' => 1, 'key' => $property->api_key, 'token' => $property->api_token]) }}" target="_blank">{{ route('api.v1.sp-mobile.playlist', ['per_page' => 5, 'page' => 1, 'key' => $property->api_key, 'token' => $property->api_token]) }}</a>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('admin/service_provider.playlist_api') }}</label>
                                        <div class="col-md-9 control-label t-a-l link-text">
                                            @if ($playlist)
                                                <a href="{{ route('api.v1.sp-mobile.playlist.videos', ['id' => $playlist->id, 'per_page' => 5, 'page' => 1, 'key' => $property->api_key, 'token' => $property->api_token]) }}" target="_blank">{{ route('api.v1.sp-mobile.playlist.videos', ['id' => $playlist->id, 'per_page' => 5, 'page' => 1, 'key' => $property->api_key, 'token' => $property->api_token]) }}</a>
                                            @else
                                                <span>{{ __('admin/service_provider.no_playlist') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </form>

                            </div><!-- .ibox-content -->

                        </div><!-- .ibox -->

                </div><!-- .col* -->

            </div><!-- .row -->

        </div><!-- .container -->

@stop