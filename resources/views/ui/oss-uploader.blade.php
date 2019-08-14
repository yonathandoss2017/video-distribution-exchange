<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <title>OSS web直传</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link href="/s3.fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
  <link href="/css/style.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
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

<body class="ibox-content">
  <div class="qq-gallery qq-uploader">
    <div class="qq-upload-button-selector" id="qq-upload-button-selector">
      <a id="selectfile" href="javascript:void(0);" class="d-flex flex-column justify-content-center align-items-center w-100 h-100" style="color: inherit;">
        <span>Drop files here or click to upload.</span>
        <span class="format">Supported formats: .MP4</span>
      </a>
    </div>
    <ul class="qq-upload-list" id="qq-upload-list"></ul>
  </div>
  <div class="modal fade" id="upload-alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
      <div class="modal-content">
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-normal" data-dismiss="modal">close</button>
        </div>
      </div>
    </div>
  </div>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/js/oss-upload.js"></script>
<script>
  var uploader = new plupload.Uploader({
    runtimes: 'html5,flash,silverlight,html4',
    browse_button: 'selectfile', //selector #selectfile
    url: 'http://oss.aliyuncs.com',
    multi_selection: false,
    drop_element: $('#qq-upload-button-selector').toArray(),
    filters: {
      mime_types: [
        { title: "video files", extensions: "mp4" }
      ],
      max_file_size: "500mb",
      prevent_duplicates: true
    },
    max_retries: 0,
    flash_swf_url: '/js/plupload/Moxie.swf',
    silverlight_xap_url: '/js/plupload/Moxie.xap',

    init: {
      PostInit: function() {
        serverUrl = '/manage/1/cp/videos/request_upload'
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
          ossUploadSuccess(up, file);
        } else {
          ossUploadFailed(up, file, info)
        }
      },

      Error: function(up, err) {
        if (err.message == "HTTP Error.") {
          ossUploadFailed(up, err.file)
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

</html>