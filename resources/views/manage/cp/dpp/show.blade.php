@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/ivideoadd/ivideoadd.dpp_playlist', ['name'=>$playlist->name]) }}
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7">
                    <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd.dpp_playlist', ['name'=>$playlist->name]) }}</div>
                </div>
                <div class="col-md-5">
                    <div class="right-panel">
                    <a href="{{ route('manage.cp.dpp.create', $property_id) . '?playlistId=' . $playlist_id }}" class="btn btn-normal btn-m">{{ __('manage/cp/ivideoadd/ivideoadd.review') }}</a>
                    </div>
                </div>
            </div>

            @include('partials.dpp_playlist_summary')

        </div>
    </div>
</div>
@stop

@push('js')
<script type="text/javascript">
    $('.fa-angle').click(function() {
        $(this).toggleClass('fa-angle-down fa-angle-up');
        $(this).closest('.ibox').find('.ibox-content').slideToggle();
    })
</script>
<script>
$(window).on('hashchange', function() {
    if (window.location.hash) {
        var page = window.location.hash.replace('#', '');
        if (page == Number.NaN || page <= 0) {
            return false;
        } else {
            getLocales(page);
        }
    }
});
$(document).ready(function() {
    $(document).on('click', '.pagination a', function (e) {
        getLocales($(this).attr('href').split('page=')[1]);
        e.preventDefault();
    });
});
function getLocales(page) {
    $.ajax({
        url : '?page=' + page,
        dataType: 'json',
    }).done(function (data) {
        $('.scene-locale .ibox-content' ).html(data);
        location.hash = page;
    }).fail(function () {
        console.log('Locales could not be loaded.');
    });
}
</script>
@endpush
