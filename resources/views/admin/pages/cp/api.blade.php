@extends('admin.layout')
@push('title') {{ __('admin/sidebar.content_providers') }} | {{ __('app.title') }} @endpush
@section('content')
    <div class="row justify-content-center">

        <header class="title-header col-md-9">
            <h3 class="title">{{ $property->name }}</h3>
        </header>

        <div class="col-md-9">
            <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.content_providers') }}</a> <span class="brdcrmb-item">/</span><strong class="brdcrmb-item">{{ __('admin/common.api') }}</strong></p>
        </div><!-- .col-* -->

        <div class="col-md-9">
            <div class="ibox">
                <div class="ibox-content" style="padding-top:20px;">
                    <center>{{ __('admin/content_provider.no_api_available_for_this_cp') }}</center>
                </div><!-- .ibox-content -->
            </div><!-- .ibox -->
        </div><!-- .col* -->

    </div><!-- .row -->
@stop
