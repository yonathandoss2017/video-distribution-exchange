@php
    $layout = 'partials.layout_error';
    if (Request::is('admin/*')) {
        $layout = 'admin.layout';
    }
@endphp
@extends($layout)

@push('title')
    {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="text-center page-error">
            <p class="page-error-type"><i class="fa fa-exclamation-triangle"></i>{{ __('errors.error') }}</p>
            <span>{{ __('errors.error_404') }}&nbsp;<a href="{{ $layout == 'admin.layout' ? route('admin.home') : route('manage.property.select') }}">{{ __('errors.back') }}</a></span>
        </div>
    </div>
@stop
