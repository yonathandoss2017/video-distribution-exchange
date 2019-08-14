@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }}
@endpush

@section('content')

<div style="width: 900px;height: 1200px; margin: auto;">
    <iframe width="900" height="1200" src="{!! $embed_url !!}" frameborder="0" style="border:0" allowfullscreen></iframe>
    
</div>

@stop