@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }} | {{ __('manage/cp/block-chain/block-chain.evidence_playlist', ['name'=>$playlist_evidence_request->playlist->name]) }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="min-menu row m-t-2">
                <div class="col-md-9">
                    <div class="title">{{ __('manage/cp/block-chain/block-chain.evidence_videos') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="right-panel">
                        <a href="{{ route('manage.cp.block-chain.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/block-chain/block-chain.back_to_block_chain') }}</a>
                    </div>
                </div>
            </div>
            <div class="ibox dpp-videos">
                <div class="ibox-content">
                    @include('manage.cp.block-chain.videos')
                 </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script>
$(document).ready(function() {
    $(document).on('click', '.dpp-videos .pagination a', function (e) {
        getVideos($(this).attr('href').split('video=')[1]);
        e.preventDefault();
    });
});
function getVideos(video) {
    $.ajax({
        url : '?video=' + video + '&type=video',
        dataType: 'json',
    }).done(function (data) {
        $('.dpp-videos .ibox-content' ).html(data);
    }).fail(function () {
        console.log('Videos could not be loaded.');
    });
}
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
