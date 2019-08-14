@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/contents/videos.edit_video') }}
@endpush

@php
    $jquery_in_head = true;
@endphp
@push('script-head')
<script src="/vendor/jquery/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="/css/datapicker/datepicker3.css" rel="stylesheet">

<script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
            {!! Form::open(['method'=>'POST', 'url' => route('manage.cp.video.update',[ $property_id, $video->id ]), 'id' => 'video_form', 'class'=>'form-horizontal', 'files' => true]) !!}
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.videos', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/videos.back_to_videos') }}</a>
                        <div class="title">{{ __('manage/cp/contents/videos.edit_video') }}</div>
                    </div>
                    @if($video->status == 'processing')
                        <div class="alert alert-warning text-center m-t">
                            {{ __('manage/cp/contents/videos.the_video_is_still_under_processing') }}
                        </div>
                    @else
                        <div class="top-video" style="margin-top: 20px">
                            <div class="video-grid embed-responsive embed-responsive-16by9" id="video"></div>
                            @if($downloadable)
                            <div class="download-video text-center">
                                <a href="javascript:showDownload();" class="btn btn-normal float-none text-uppercase">{{ __('manage/cp/contents/videos.download_video') }}</a>
                            </div>
                            @endif
                        </div>
                        <div class="alert alert-danger" role="alert" style="display:none;"></div>
                    @endif
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/contents/videos.video_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                                <div class="form-group row{{ $errors->has('title') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('common.title') }}*</label>
                                    <div class="col-md-9">
                                        <input type="text" name="title" value="{{ $video->name }}" class="form-control {{ $errors->has('title') ? ' form-control-danger ' : ''}}" required="">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'title'])
                                    </div>
                                </div>

                                 <div class="form-group row{{ $errors->has('description') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.descriptions') }}</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control-area" id="exampleTextarea" name="description" rows="3">{{ $video->description }}</textarea>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'description'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('playlist') ? ' has-danger ' : '' }}">
                                    <label class="col-md-3 control-label">{{ __('common.playlist') }}*</label>
                                    <div class="col-md-9">
                                        <select name="playlist[]" class="select2 form-control {{ $errors->has('playlist') ? 'form-control-danger ' : ''}}" id="id_label_multiple" multiple="multiple">
                                        @if( count($playlists) > 0 && is_null(old('playlist')) )
                                            @foreach($playlists as $p)
                                                @foreach($video->playlists as $v)
                                                    <option value="{{ $p->id }}" @if($v->id == $p->id) selected="selected" @endif>{{ $p->name }}</option>
                                                @endforeach
                                            @endforeach
                                        @endif
                                        </select>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'playlist'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('tags') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.tags') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="tags" value="{{ old('tags', $video->metadata->tags ?? '') }}" tabindex="3">
                                        <div class="description">{{ __('manage/cp/contents/videos.separate_tags_with_commas') }}</div>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'tags'])
                                    </div>
                                </div>

                                <div class="form-group row{{ $errors->has('produced_at') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.make_date') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group date">
                                        <input type="text" name="produced_at" value="{{ old('produced_at', $video->produced_at ?? '') }}" class="form-control" autocomplete="false">
                                        <span class="input-group-addon">
                                          <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'produced_at'])
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/contents/videos.featured_image_upload') }}</h5>
                            <input type="hidden" id="keyNameObj">
                            <input type="hidden" class="thumbnailUrl-s3" name="thumbnailUrl">
                        </div>
                        <div class="ibox-content">

                            {!! \App\Services\FeaturedImageService::getHtml($video->image_path, \App\Services\Serve\VideoImageService::getImageUrl($video, $video->property_id, null, 780)) !!}
                        </div>
                    </div>

                    <!-- MODAL -->
                    <div id="modal"></div>

                    @if($video->platformAlivodTranscodes)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/contents/videos.video_price') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.definition') }}</label>
                                <div class="col-md-9">
                                    @php
                                        $platformAlivodTranscodes = $video->platformAlivodTranscodes->chunk(2)
                                    @endphp
                                    @foreach($platformAlivodTranscodes as $alivodTranscodes)
                                        <div class="row {{ $loop->last ? '' : 'resolution' }}" {{ $loop->last ? '' : 'style="margin-bottom: 10px;"' }}>
                                            @foreach($alivodTranscodes as $transcode)
                                                <div class="col-md-6 d-flex">
                                                    <label class="control-label t-a-l m-r">{{ __('resolution.'.strtolower($transcode->definition)) }}</label>
                                                    <input class="form-control short-3" name="price_{{ $transcode->definition }}" value="{{ $transcode->price }}" type="number" min="0.00" step="0.01">
                                                    <label class="control-label t-a-l">￥</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.notes') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" rows="3" name="price_note" tabindex="2">{{ $video->price_note }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <p>&nbsp;</p>
                    <div class="title-header">
                        <div class="title">{{ __('manage/cp/contents/videos.video_censorship') }}</div>
                    </div> <!-- /.title-header -->

                    @if (config('features.content_review'))
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
                    @endif

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

                    <input name="is_submit" type="hidden" value="">
                    <div class="form-save">
                        <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                        @if(config('features.content_review'))
                            @unless(\App\Models\EntryBase::STATUS_PROCESSING == $video->status)
                                <button type="button" class="btn btn-primary" onclick="submitForm()">{{ __('common.save_and_submit') }}</button>
                            @endunless
                        @endif
                    </div>
                </div>
            {{ Form::close() }}
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

    <div class="modal fade" id="modal-download-url" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog s-width" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/contents/videos.download_video') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="wizard-alert alert alert-success hidden" role="alert" id="copy-succ-message">
                        <span>{{ __('manage/cp/contents/videos.successful_copy') }}</span>
                    </div>
                    <div class="ibox-content">
                        <p>{{ __('manage/cp/contents/videos.download_tips') }}</p>
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="source_url" readonly value="{{ $downloadUrl }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="copyToClipboard()">{{ __('common.copy') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script type="text/javascript" src="{!! asset('js/datapicker/bootstrap-datepicker.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/datapicker/bootstrap-datepicker.zh.min.js') !!}"></script>
<script type="text/javascript">
        var entry_id = "{{ $video->id }}" ;
        var prop_id = "{{ $property_id }}" ;
        var is_edit_form = true;
        var is_published = "{{ $video->is_published }}";
        var videoStreamUrl = "{!!  $videoStreamUrl ?? null !!}";
</script>
{!! \App\Services\Serve\VideoPlayerService::player($property, $video, "video") !!}
<script>
    var data = [];
    <?php if (is_array($playlists) && count($playlists) > 0) {
    foreach ($playlists as $p) {
        ?>
        data.push({ id: "{{ $p->id }}", text: "{{ $p->name }}"});

        <?php if (is_array(old('playlist'))) {
            if (in_array($p->id, old('playlist'))) {?>
                $('.select2').append($("<option></option>").attr("value","{{ $p->id }}").attr("selected","selected").text("{{ $p->name }}"));
        <?php }}?>
    <?php }}?>

    $('.select2').select2({
        placeholder: "-- {{ __('manage/cp/contents/videos.select_playlist') }} --",
        data: data
    });
</script>
<script>
    function submitForm() {
        $("input[name='is_submit']").val(true)
        $( "#video_form" ).submit();
    }
</script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.ai-review .pagination a', function (e) {
            getVideoAiReviewResults($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
    });

    $('.input-group.date').datepicker({
        todayBtn: "linked",
        language: "{{ Session::get('locale') }}",
        keyboardNavigation: false,
        calendarWeeks: true
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

    function showDownload()
    {
        $("#copy-succ-message").addClass('hidden');
        $('#modal-download-url').modal('show');
    }

    function copyToClipboard() {
        $('#source_url').select();
        document.execCommand("copy");
        $("#copy-succ-message").removeClass('hidden');
    }
</script>
@endpush
