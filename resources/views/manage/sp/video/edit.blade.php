@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/content/video.edit_video') }} | {{ __('app.title') }}
@endpush

@php
    $jquery_in_head = true;
@endphp
@push('script-head')
    <script src="/vendor/jquery/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
@endpush

@include('partials.manager.video.edit', ['type' => 'sp'])
