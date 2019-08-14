
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
            {!! Form::open(['method'=>'POST', 'url' => route('manage.' . $type . '.video.update',[ $property_id, $video->id ]), 'class'=>'form-horizontal', 'files' => true]) !!}
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.' . $type . '.videos', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/' . $type . '/content/video.back_to_videos') }}</a>
                        <div class="title">{{ __('manage/' . $type . '/content/video.edit_video') }}</div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/' . $type . '/content/video.video_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row{{ $errors->has('title') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/sp/common.title') }}</label>
                                <div class="col-md-9">
                                    <input type="text" name="title" value="{{ VideoDetailsService::getTitle($video, $property_id) }}" class="form-control {{ $errors->has('title') ? ' form-control-danger ' : ''}}" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'title'])
                                </div>
                            </div>

                             <div class="form-group row{{ $errors->has('description') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/' . $type . '/content/video.descriptions') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" id="exampleTextarea" name="description" rows="3">{!! VideoDetailsService::getDescription($video, $property_id) !!}</textarea>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'description'])
                                </div>
                            </div>

                            {!! \App\Services\FeaturedImageService::getHtml($video->image_path, \App\Services\Serve\VideoImageService::getImageUrl($video, $property_id, null, 780)) !!}
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/common.video') }}</label>
                                <div class="col-md-9 maxWidth-465">
                                    <div class="video home">
                                        <div class="video-grid embed-responsive embed-responsive-16by9" id="video"></div>
                                    </div>
                                    <div class="download-video text-center">
                                        <a href="javascript:showDownload();" class="btn btn-normal float-none text-uppercase" style="margin-bottom: 20px;">{{ __('manage/cp/contents/videos.download_video') }}</a>
                                    </div>
                                </div>
                                <div class="alert alert-info text-center" role="alert" style="display:none; width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL -->
                    <div id="modal"></div>

                    <div class="form-save">
                        <button type="submit" class="btn btn-primary">{{ __('manage/sp/common.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}
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
                                    <input type="text" class="form-control" id="source_url" readonly value="{{ $video->source_url }}">
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
{!! \App\Services\Serve\VideoPlayerService::player($property, $video, "video") !!}
<script>
    var data = [];
    <?php if (count($video->playlists) > 0) {
    foreach ($video->playlists as $p) {
        ?>
        data.push({ id: "{{ $p->id }}", text: "{{ $p->name }}"});

        <?php if (is_array(old('playlist'))) {
            if (in_array($p->id, old('playlist'))) {?>
                $('.select2').append($("<option></option>").attr("value","{{ $p->id }}").attr("selected","selected").text("{{ $p->name }}"));
        <?php }}?>
    <?php }}?>

    $('.select2').select2({
        placeholder: "-- {{ __('manage/' . $type . '/content/video.select_playlist') }} --",
        data: data
    });

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