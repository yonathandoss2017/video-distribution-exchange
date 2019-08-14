@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/contents/videos.new_video') }}
@endpush

@php
    $jquery_in_head = true;
@endphp
@push('script-head')
<script src="/vendor/jquery/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="/css/datapicker/datepicker3.css" rel="stylesheet">
<link href="/fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet">
<script type="text/javascript" src="/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/js/oss-upload.js"></script>

<script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
<style>
.qq-thumbnail-wrapper {
  background: #EDEDED;
}

.qq-gallery .qq-upload-retry {
  margin-left: 0;
  transform: translateX(-50%);
}

.qq-gallery .qq-upload-spinner {
  z-index: 1;
}
</style>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                {!! Form::open(['route' => ['manage.cp.store.video', $property_id], 'method' => 'post', 'id' => 'video_form', 'class'=>'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.videos', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/videos.back_to_videos') }}</a>
                        <div class="title">{{ __('manage/cp/contents/videos.new_video') }}</div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/contents/videos.video_upload') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">
                                    {{ __('manage/cp/contents/videos.upload_method') }}*
                                    <input type="hidden" name="direct_upload" id="directUpload" value="{{ old('direct_upload') }}">
                                </label>
                                <div class="col-md-9">
                                    <div class="custom-controls-stacked">
                                        <label class="custom-control custom-radio m-b-15">
                                            <input name="video_method" type="radio" id="direct_oss" class="custom-control-input video_method http_upload" value="direct_oss" {{ (old('video_method') == 'direct_oss' ? 'checked' : "") }} tabindex="10" required>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/contents/videos.direct_upload') }}</span>
                                        </label>
                                        <div class="form-group row direct-upload">
                                            <div class="col-md-12 maxWidth-475">
                                                <input type="hidden" value="" name="video_name" id="video-name"/>
                                                <input type="hidden" value="" name="video_path" id="video-path"/>
                                                <div class="qq-gallery qq-uploader">
                                                    <div class="qq-upload-button-selector" id="qq-upload-button-selector">
                                                    <a id="selectfile" href="javascript:void(0);" class="d-flex flex-column justify-content-center align-items-center w-100 h-100" style="color: inherit;">
                                                        <span>{{ __('manage/cp/contents/videos.drop_files_here') }}</span>
                                                        <span class="format" style="padding-left: 15px;">{{ __('manage/cp/contents/videos.supported_formats') }}: 3GP、ASF、AVI、DAT、DV、FLV、F4V、GIF、M2T、M4V、MJ2、MJPEG、MKV、MOV、MP4、MPE、MPG、MPEG、MTS、OGG、QT、RM、RMVB、SWF、TS、VOB、WMV、WEBM</span>
                                                    </a>
                                                    </div>
                                                    <ul class="qq-upload-list" id="qq-upload-list"></ul>
                                                </div>
                                                <div class="modal fade" id="upload-alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog s-width" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body"></div>
                                                        <div class="modal-footer">
                                                        <button type="button" class="btn btn-normal" data-dismiss="modal">{{ __('manage/cp/contents/videos.close') }}</button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                                <script>
                                                    var uploader = new plupload.Uploader({
                                                        runtimes: 'html5,flash,silverlight,html4',
                                                        browse_button: 'selectfile', //selector #selectfile
                                                        url: 'http://oss.aliyuncs.com',
                                                        multi_selection: false,
                                                        drop_element: $('#qq-upload-button-selector').toArray(),
                                                        filters: {
                                                        mime_types: [
                                                            { title: "video files", extensions: "3gp,asf,avi,dat,dv,flv,f4v,gif,m2t,m4v,mj2,mjpeg,mkv,mov,mp4,mpe,mpg,mpeg,mts,ogg,qt,rm,rmvb,swf,ts,vob,wmv,webm" }
                                                        ],
                                                        max_file_size: "0",
                                                        prevent_duplicates: true
                                                        },
                                                        max_retries: 0,
                                                        flash_swf_url: '/js/plupload/Moxie.swf',
                                                        silverlight_xap_url: '/js/plupload/Moxie.xap',

                                                        init: {
                                                        PostInit: function() {
                                                            serverUrl = '{{ route('manage.cp.request-upload', $property_id) }}'
                                                        },

                                                        FilesAdded: function(up, files) {
                                                            $(".form-save button").attr("disabled", true)
                                                            plupload.each(files, function(file) {
                                                            $('#qq-upload-list').append(
                                                                '<li class="qq-file" id="' + file.id + '">' +
                                                                '<span role="status" class="qq-upload-status-text-selector qq-upload-status-text qq-hide">Upload failed</span>' +
                                                                '<div class="qq-progress-bar-container-selector qq-progress-bar-container">' +
                                                                '<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar" style="min-width: 2px"></div>' +
                                                                '</div>' +
                                                                '<span class="qq-upload-spinner-selector qq-upload-spinner"></span>' +
                                                                '<div class="qq-thumbnail-wrapper d-flex justify-content-center align-items-center">' +
                                                                '<img class="qq-thumbnail-selector" src="https://exchange.ivideocloud.cn/s3.fine-uploader/placeholders/not_available-generic.png" style="width: 25%; top: initial;">' +
                                                                '</div>' +
                                                                '<button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>' +
                                                                '<button type="button" class="qq-upload-retry-selector qq-upload-retry qq-hide">' +
                                                                '<span class="qq-btn qq-retry-icon" aria-label="Retry"></span>' +
                                                                '</button>' +
                                                                '<div class="qq-file-info">' +
                                                                '<div class="qq-file-name">' +
                                                                '<span class="qq-upload-file-selector qq-upload-file">' + file.name + '</span>' +
                                                                '</div>' +
                                                                '<div class="d-flex justify-content-between align-items-center">' +
                                                                '<span class="qq-upload-size-selector qq-upload-size"></span>' +
                                                                '<button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete qq-hide" >' +
                                                                '<span class="qq-btn qq-delete-icon" aria-label="Delete"></span>' +
                                                                '</button>' +
                                                                '</div>' +
                                                                '</div>' +
                                                                '</li>'
                                                            );
                                                            setUploadParam(up, file.name, serverUrl);
                                                            $('#qq-upload-button-selector').hide()
                                                            });
                                                        },

                                                        BeforeUpload: function(up, file) {
                                                        },

                                                        UploadProgress: function(up, file) {
                                                            ossUploadProgress(up, file)
                                                        },

                                                        FilesRemoved: function(up, files) {
                                                            plupload.each(files, function(file) {
                                                            $('li#' + file.id).remove()
                                                            });
                                                            $('#qq-upload-button-selector').show()
                                                        },

                                                        FileUploaded: function(up, file, info) {
                                                            if (info.status == 200) {
                                                            ossUploadSuccess(up, file);
                                                            } else {
                                                            ossUploadFailed(up, file)
                                                            }
                                                        },

                                                        Error: function(up, err) {
                                                            if (err.message == "HTTP Error.") {
                                                            ossUploadFailed(up, err.file, err.message)
                                                            } else {
                                                            $('#upload-alert .modal-body').text(err.message)
                                                            $('#upload-alert').modal('show')
                                                            }
                                                            console.log(err.file)
                                                        }
                                                        }
                                                    });

                                                    uploader.init();
                                                </script>
                                                <div class="error_video_s3{{ $errors->has('video_path') ? " has-danger" : "" }}">
                                                    @include('partials.errors', ['err_type' => 'field','field' => 'video_path'])
                                                </div>
                                            </div>
                                        </div>
                                        <label class="custom-control custom-radio m-b-15">
                                            <input name="video_method" type="radio" id="oss_url" class="custom-control-input video_method upload_url" value="oss_url" {{ (old('video_method') == 'oss_url' ? 'checked' : "") }} tabindex="11" required>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/cp/contents/videos.upload_by_url') }}</span>
                                        </label>
                                        <div class="form-group row{{ $errors->has('video_url') ? ' has-danger ' : '' }} url">
                                            <div class="col-md-12 maxWidth-475">
                                                <input type="text" id="video_url" class="form-control {{ $errors->has('video_url') ? ' form-control-danger ' : '' }}video_url" name="video_url" value="{{ old('video_url') }}">
                                                <div class="description">
                                                    <small>* {{ __('manage/cp/contents/videos.supported_formats') }}: 3GP、ASF、AVI、DAT、DV、FLV、F4V、GIF、M2T、M4V、MJ2、MJPEG、MKV、MOV、MP4、MPE、MPG、MPEG、MTS、OGG、QT、RM、RMVB、SWF、TS、VOB、WMV、WEBM</small>
                                                </div>
                                                <div id="error_url_video" class="{{ $errors->has('direct_upload') ? " has-danger" : "" }}">
                                                    @include('partials.errors', ['err_type' => 'field','field' => 'video_url'])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="error_video_upload" class="{{ $errors->has('video_method') ? " has-danger" : "" }}">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'video_method'])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/contents/videos.video_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row{{ $errors->has('title') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('common.title') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control vidtitle" name="title" value="{{ old('title') }}" tabindex="1" required>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'title'])
                                </div>
                            </div>

                             <div class="form-group row{{ $errors->has('description') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.descriptions') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" id="exampleTextarea" rows="3" name="description" tabindex="2">{{ old('description') }}</textarea>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'description'])
                                </div>
                            </div>

                            <div class="form-group row{{ $errors->has('playlist') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('common.playlist') }}*</label>
                                 <div class="col-md-9">

                                    {{ Form::select('playlist[]', $playlists, null, ["class"=>"select2 form-control", "id"=>"id_label_multiple", "multiple"=>"multiple", "required" => "true"]) }}

                                    @if ( old('playlist') && is_array(old('playlist')) )
                                        <script type="text/javascript">
                                            $(function() {
                                                var playlistIds = [
                                                    @foreach (old('playlist') as $key => $playlist)
                                                        "{{ $playlist }}",
                                                    @endforeach
                                                ];
                                                $("select[name^='playlist']").val( playlistIds );
                                                $('select').trigger('change');
                                            });
                                        </script>
                                    @endif

                                    @include('partials.errors', ['err_type' => 'field','field' => 'playlist'])
                                </div>
                            </div>

                            <div class="form-group row{{ $errors->has('tags') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.tags') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="tags" value="{{ old('tags') }}" tabindex="3">
                                    <div class="description">{{ __('manage/cp/contents/videos.separate_tags_with_commas') }}</div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'tags'])
                                </div>
                            </div>

                            <div class="form-group row{{ $errors->has('produced_at') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.make_date') }}</label>
                                <div class="col-md-9">
                                    <div class="input-group date">
                                        <input type="text" name="produced_at" class="form-control" autocomplete="false">
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
                        </div>
                        <div class="ibox-content">

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/contents/videos.upload_method') }}*</label>
                                <div class="col-md-9">
                                    <label class="custom-control custom-radio">
                                        <input id="img-auto" name="img_method" type="radio" class="custom-control-input img-method" value="img_auto" {{ (old('img_method') == 'img_auto' ? 'checked' : "") }} tabindex="7" required>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('manage/cp/contents/videos.auto_generate_from_video') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input id="img-upload" name="img_method" type="radio" class="custom-control-input img-method" value="img_direct" {{ (old('img_method') == 'img_direct' ? 'checked' : "") }} tabindex="5" required>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('manage/cp/contents/videos.direct_upload') }}</span>
                                    </label>
                                    <div id="error_img_method" class="{{ $errors->has('img_method') ? ' has-danger ' : '' }}">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'img_method'])
                                    </div>
                                </div>
                            </div>

                            <div class="img-direct-upload-container" style="display: none;">
                                {!! \App\Services\FeaturedImageService::getHtml() !!}
                            </div>

                        </div>
                    </div>
                    <div class="form-save">
                        <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop

@push('js')
<script type="text/javascript">
    var is_edit_form = false;
    var is_published = false;
</script>
<script type="text/javascript" src="{!! asset('js/cp_video/new_video.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/datapicker/bootstrap-datepicker.js') !!}"></script>
<script type="text/javascript" src="{!! asset('js/datapicker/bootstrap-datepicker.zh.min.js') !!}"></script>
<script>
    $('.select2').select2({
  placeholder: "-- {{ __('manage/cp/contents/videos.select_playlist') }} --"
});
</script>
<script>
    $(document).ready(function(){
        $('#video_url').on('click', function(event) {
            $('#oss_url').attr('checked', 'checked');
        });
    });
    $('.input-group.date').datepicker({
        todayBtn: "linked",
        language: "{{ Session::get('locale') }}",
        keyboardNavigation: false,
        calendarWeeks: true
    });
</script>
@endpush
