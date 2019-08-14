@php
$jquery_in_head = true;
@endphp
@extends('partials.layout_dpp')

@push('title')
iVideoExchange | iVideoAdd Video
@endpush

@push('header_scripts')
<link href="/fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
<script src="/vendor/jquery/jquery.min.js"></script>
<script type="text/javascript" src="/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/js/oss-upload.js"></script>
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header ">
                <a href="{{ route('dpp.request.show', $playlist_id) }}" class="btn btn-normal btn-m">{{ __('dpp.back_to_dpp_video') }}</a>
                <div class="title">{{ __('dpp.manage_scenes').$entry->name }}</div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="upload-scene">
                            <input type="hidden" value="" name="video_name" id="video-name"/>
                            <input type="hidden" value="" name="video_path" id="video-path"/>
                        <div class="dpp-upload-scenes qq-gallery qq-uploader">
                            <div class="qq-upload-button-selector" id="qq-upload-button-selector">
                                <a id="selectfile" href="javascript:void(0);" class="d-flex flex-column justify-content-center align-items-center w-100 h-100" style="color: inherit;">
                                    <span>{{ __('dpp.upload_tips') }}.</span>
                                </a>
                            </div>
                            <ul class="qq-upload-list" id="qq-upload-list"></ul>
                        </div>
                        <div class="description"><small>* {{ __('dpp.supported_format') }}: .JPEG, .JPG, .PNG, .GIF</small></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="ibox dpp-upload-scenes">
					    <div class="ibox-content">
                            <div class="playlist-list">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th class="playlist-title ptl">{{ __('dpp.scene_type') }}</th>
                                        <th>{{ __('dpp.total_scenes') }}</th>
                                        <th>{{ __('dpp.total_dpp_time') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $scene_summary as $scene)
                                        <tr>
                                            <td class="playlist-title ptl">
                                                <a href="#">{{ __('dpp.scene_type_'.$scene['type']) }}</a>
                                            </td>
                                            <td class="playlist-amount">
                                                <span class="amount">{{ $scene['scenes_num'] }}</span> <small>{{ __('dpp.scenes') }}</small>
                                            </td>
                                            <td class="right"><span class="amount">{{ $scene['dpp_sum'] }}</span> <small>{{ __('dpp.seconds') }}</small></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

            <div class="row platforms row-eq-height" id="scenes">
            @foreach( $entry->scenes as $scene)
                <div class="col-md-2 scenes-m">
                    <div class="video-img main">
                        <a href="javascript:void(0)"><img src="{{ \App\Services\FeaturedImageService::generateImageUrl($scene->image_path) }}" alt=""></a>
                    </div>
                    <div class="info">
                        <div class="video-title">{{ $scene->name }}</div>
                        <div class="small-detail m-t-sm">
                            <a href="{{ route('dpp.request.scene.edit', [$playlist_id, $entry->id, $scene->id]) }}" class="btn btn-normal btn-m no-float">{{ __('common.edit') }}</a>
                            <div class="genre-info">
                                {{ Form::open(['url'=>route('dpp.request.scene.destroy',[$playlist_id, $entry->id, $scene->id]),'onsubmit' => 'return ConfirmDelete()']) }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-normal btn-m delete">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                                {{ Form::close() }}
                            </div>
                        </div>
                        <div class="clearfix"> </div>
                    </div>
                </div>
            @endforeach
            </div>
            <div class="m-t">{{ __('dpp.total_scenes_uploaded', ['scenes'=> $entry->scenes->count()]) }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="upload-alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
        <div class="modal-content">
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-normal" data-dismiss="modal">{{ __('dpp.close') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'selectfile',    //selector #selectfile
        url: 'http://oss.aliyuncs.com',
        multi_selection: false,
        drop_element: $('#qq-upload-button-selector').toArray(),
        filters: {
            mime_types: [
                {
                    title: "picture files",
                    extensions: "jpg,jpeg,png,gif"
                }
            ],
            max_file_size: "50mb",
            prevent_duplicates: true
        },
        max_retries: 0,
        flash_swf_url: '/js/plupload/Moxie.swf',
        silverlight_xap_url: '/js/plupload/Moxie.xap',
        init: {
            PostInit: function() {
                serverUrl = '{{ route('manage.cp.request-upload', $entry->property_id) }}'
            },
            FilesAdded: function(up, files) {
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
                    storeFile(file);
                    $('li#' + file.id).find(".qq-upload-cancel-selector").hide()
                } else {
                    ossUploadFailed(up, file)
                }
            },
            Error: function(up, err) {
                if (err.message == "HTTP Error.") {
                    ossUploadFailed(up, err.file, err.message)
                } else {
                    showModel(err.message);
                }
            }
        }
    });

    var storeFile = function (file) {
        var image_path = $('#video-path').val();
        var scenes_name = $('#video-name').val();
        $.ajax({
            method: 'POST',
            url:'{{ route('dpp.request.scene.store', [$playlist_id, $entry->id]) }}',
            data: {entry_id : '{{ $entry->id }}', image_path : image_path, scenes_name : scenes_name},
            dataType: 'json',
            success: function( data ) {
                if (data.statusCode == 200) {
                    var editUrl = '{{ route('dpp.request.scene.edit', [$playlist_id, $entry->id, 'scene_id']) }}';
                    var deleteUrl = '{{ Form::open(['url'=>route('dpp.request.scene.destroy',[$playlist_id, $entry->id, 'scene_id']),'onsubmit' => 'return ConfirmDelete()']) }}';
                    editUrl = editUrl.replace('scene_id',data.scene.id);
                    deleteUrl = deleteUrl.replace('scene_id',data.scene.id);
                    $("#scenes").append(
                        '<div class="col-md-2 scenes-m">'+
                        '<div class="video-img main">'+
                        '<a href="javascript:void(0)"><img src="'+ data.scene.image_path +'" alt=""></a>'+
                        '</div>'+
                        '<div class="info">'+
                        '<div class="video-title">'+data.scene.name+'</div>'+
                        '<div class="small-detail m-t-sm">'+
                        '<a href="'+editUrl+'" class="btn btn-normal btn-m no-float">{{ __('common.edit') }}</a>'+
                        '<div class="genre-info">'+
                        deleteUrl+
                        '{{ method_field('DELETE') }}'+
                        '<button type="submit" class="btn btn-normal btn-m delete">'+
                        '<i class="fa fa-trash" aria-hidden="true"></i>'+
                        '</button>'+
                        '{{ Form::close() }}'+
                        '</div>'+
                        '</div>'+
                        '<div class="clearfix"> </div>'+
                        '</div>'+
                        '</div>'
                    );
                    $('#scenes-name').val('');
                    $('#image-path').val('');
                    $('li#' + file.id).hide();
                    $('#qq-upload-button-selector').show()
                } else {
                    showModel(data.message);
                }
            },
            error: function(xhr, type){
                console.error('search get error!');
            }
        });
    }
    var showModel = function (message) {
        $('#upload-alert .modal-body').text(message)
        $('#upload-alert').modal('show')
    }

    uploader.init();
</script>
@endpush
