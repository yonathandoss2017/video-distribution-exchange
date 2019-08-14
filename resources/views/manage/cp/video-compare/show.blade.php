@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/video-compare/compare.video_contrast_result', ['name'=>$videoCompare->title]) }}
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-7">
                    <div class="title">{{ __('manage/cp/video-compare/compare.video_compare_result', ['name'=>$videoCompare->title]) }}</div>
                </div>
                <div class="col-md-5">
                    <div class="right-panel">
                        <a href="{{ route('manage.cp.block-chain.video-compare.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/video-compare/compare.back_to_compare') }}</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/video-compare/compare.summary') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <form class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.summary_total_video') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                        {{ $compare_video_count }} {{ __('manage/cp/video-compare/compare.video') }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.summary_total_match_video') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                        {{ $videoCompare->compare_results_count }} {{ __('manage/cp/video-compare/compare.video') }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.summary_total_match_fragment') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                        {{ $match_fragment_count }} {{ __('manage/cp/video-compare/compare.fragment') }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.summary_total_match_duration') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                        {{ $match_duration_count / 1000 }} {{ __('manage/cp/video-compare/compare.second') }}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/video-compare/compare.match_result_analysis') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <form class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.max_confidence') }}</label>
                                    <div class="col-md-7 control-label t-a-l">{{ $max_similarity }}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.max_match_duration') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                       {{ $match_max_duration / 1000 }} {{ __('manage/cp/video-compare/compare.second') }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-5 control-label">{{ __('manage/cp/video-compare/compare.single_video_max_match_fragment') }}</label>
                                    <div class="col-md-7 control-label t-a-l">
                                        {{ $match_max_fragment }} {{ __('manage/cp/video-compare/compare.fragment') }}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <div class="min-menu row m-t-2">
                <div class="col-md-9">
                    <div class="title">{{ __('manage/cp/video-compare/compare.compare_videos') }}</div>
                </div>
            </div>

            <div class="ibox contrast-videos">
                <div class="ibox-content">
                    @include('manage.cp.video-compare.show_videos')
                </div>
            </div>

        </div>
    </div>
</div>
@stop
@push('js')
    <script type="text/javascript">
        $(window).on('hashchange', function() {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getVideos(page);
                }
            }
        });
        $(document).ready(function() {
            $(document).on('click', '.pagination a', function (e) {
                getVideos($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });
        });
        function getVideos(page) {
            $.ajax({
                url : '?page=' + page,
                dataType: 'html',
            }).done(function (data) {
                $('.contrast-videos .ibox-content').html(data);
                location.hash = page;
            }).fail(function () {
            });
        }
        function showToggle(obj) {
            $(obj).toggleClass('fa-angle-down fa-angle-up');
            $(obj).parent().parent().next('.fragments').slideToggle();
        }
    </script>
@endpush
