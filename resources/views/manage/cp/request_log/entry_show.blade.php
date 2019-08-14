@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.request-logs.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/request_logs.back_to_request_logs') }}</a>
                        <div class="title">{{ $video->name }}</div>
                    </div>
                    <div class="top-video" style="margin-top: 20px">
                        <div class="video-grid embed-responsive embed-responsive-16by9" id="video"></div>
                    </div>
                    <form method="get" class="form-horizontal">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/request_logs.video_information') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('common.title') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $video->name }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/request_logs.descriptions') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $video->description }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('common.playlist') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ implode(',', $video->playlists->pluck('name')->toArray()) }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/request_logs.tags') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $video->metadata->tags ?? '-' }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/request_logs.released_at') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        @if($video->published_at)
                                            <div id="{{ $video->id }}-{{ $video->published_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $video->id }}" dt="{{ $video->published_at->timestamp }}"></small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/request_logs.featured_image') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row img-direct-upload">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/request_logs.featured_image') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        <div class="creative-image" style="margin-top: 0px;">
                                            <img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($video, $property_id) }}" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>&nbsp;</p>
                        <div class="title-header">
                            <div class="title">{{ __('manage/cp/contents/videos.video_censorship') }}</div>
                        </div> <!-- /.title-header -->

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/contents/videos.review_comments') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form form-horizontal">
                                    <div class="form-group row">
                                        <div class="col-md-12 control-label t-a-l">{{ empty($video->comment) ? '-' : $video->comment }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('view-ai-review', $property_id)
                        @if($aiReviewVideoResults->count() > 0)
                            <div class="ibox ai-review">
                                <div class="ibox-title">
                                    <h5>{{ __('manage/cp/contents/videos.ai_censorship') }}</h5>
                                </div>
                                <div class="ibox-content pt-0">
                                    @include('manage.cp.ai_review_video_result')
                                </div>
                            </div>
                        @endif
                        @endcan
                    </form>

                    <!-- MODAL -->
                    <div id="modal"></div>
                    @if (!$isContentUploader)
                        <div class="form-save">
                            <form method="post" action="{{ route('manage.cp.request-logs.approve', [$property_id, $video->id]) }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="Entry">
                                <button type="submit" class="btn btn-normal btn-m m-r">{{ __('manage/cp/contents/request_logs.approve') }}</button>
                            </form>
                            <form method="post" action="{{ route('manage.cp.request-logs.reject', [$property_id, $video->id]) }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="Entry">
                                <button type="submit" class="btn btn-normal btn-m m-r">{{ __('manage/cp/contents/request_logs.reject') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade img-detail" id="modal-img" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body preview_image">
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
{!! \App\Services\Serve\VideoPlayerService::player($property, $video, "video") !!}
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.ai-review .pagination a', function (e) {
            getVideoAiReviewResults($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
    });
    var request_url = "{{ route('manage.cp.request-logs.ai-review.result', [$property_id, $video->id]) }}";
    function getVideoAiReviewResults(page) {
        $.ajax({
            url : request_url + '?page=' + page,
            dataType: 'html',
        }).done(function (data) {
            $('.ai-review .ibox-content').html(data);
        }).fail(function () {
        });
    }
    function showImage(obj) {
        $('.preview_image').html('<img src="'+$(obj).attr('src')+'">');
        $('#modal-img').modal('show');
    }
</script>
@endpush
