@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }} | {{ __('manage/cp/ivideoadd/ivideoadd.dpp_playlist', ['name'=>$playlist->name]) }}
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
            <div class="title-header row">
                <div class="title col-md-6">{{ __('manage/cp/ivideoadd/ivideoadd.dpp_playlist', ['name'=>$playlist->name]) }}</div>
                    <div class="col-md-6">
                        @if ($playlist->dpp_status == \App\Models\Playlist::DPP_STATUS_REVIEW)
                            <a href="#" class="btn btn-normal btn-m m-l-15" data-toggle="modal" data-target="#publish">{{ __('manage/cp/ivideoadd/ivideoadd.publish_for_ad') }}</a>
                        @endif
                    </div>
            </div>

            @include('partials.dpp_playlist_summary')

            <div class="min-menu row m-t-2">
                <div class="col-md-9">
                    <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd.dpp_videos') }}</div>
                </div>
                <div class="col-md-3">
                    <div class="right-panel">
                        <div class="status-float">
                        <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('manage/cp/ivideoadd/ivideoadd.status_filter') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist->id]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_all') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist->id, 'status'=>\App\Models\Entry::DPP_STATUS_PUBLISHED]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_published') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist->id, 'status'=>\App\Models\Entry::DPP_STATUS_REVIEW]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_review') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist->id, 'status'=>\App\Models\Entry::DPP_STATUS_PROCESSING]) }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_processing') }}</a></li>
                        </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox dpp-videos">
                <div class="ibox-content">
                    @include('manage.cp.dpp.videos')
                 </div>
            </div>
            <!-- MODAL -->
            <div class="modal fade" id="publish" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog s-width" role="document">
                    <div class="modal-content">
                     <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/ivideoadd/ivideoadd.publish_for_ad') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>{!! __('manage/cp/ivideoadd/ivideoadd.publish_content',['number'=>$review_scenes_count]) !!}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="publishPlaylist();" class="btn btn-secondary">{{ __('common.publish') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script type="text/javascript">
    $('.fa-angle').click(function() {
        $(this).toggleClass('fa-angle-down fa-angle-up');
        $(this).closest('.ibox').find('.ibox-content').slideToggle();
    });
</script>
<script>
    {{--var udpateUrl = '{{ route('dpp.request.update', ['playlist_id' => $playlist->id]) }}';--}}
    function publishPlaylist() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.params.csrfToken
            }
        });
        $.ajax({
            type: 'PUT',
            url: '{{ route('manage.cp.dpp.update', [$property_id, $playlist->id]) }}',
            success: function (data) {
                window.location.reload();
            },
            error: function (data) {
            }
        });
    }
</script>
<script>
$(document).ready(function() {
    $(document).on('click', '.scene-locale .pagination a', function (e) {
        getLocales($(this).attr('href').split('locale=')[1]);
        e.preventDefault();
    });
});
function getLocales(locale) {
    $.ajax({
        url : '?locales=' + locale + '&type=locale',
        dataType: 'json',
    }).done(function (data) {
        $('.scene-locale .ibox-content' ).html(data);
    }).fail(function () {
        console.log('Locales could not be loaded.');
    });
}
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
