var setUploadParam = function(up, filename, serverUrl) {
  $.ajax({
    method: "GET",
    url: serverUrl,
    data: { filename: filename },
  }).done(function(data) {
    $('#video-name').val(data.filename);
    $('#video-path').val(data.key + data.filename);
    up.setOption({
      'url': data.host,
      'multipart_params': {
        'key': data.key + data.filename,
        'policy': data.policy,
        'OSSAccessKeyId': data.accessid,
        'success_action_status': '200', //让服务端返回200,不然，默认会返回204
        'signature': data.signature,
      }
    });
    up.start();
  });
};

var ossUploadProgress = function(up, file) {
  var fileItem = $('li#' + file.id)
  fileItem.find('.qq-upload-size').text(file.percent + "% of " + plupload.formatSize(file.size))
  fileItem.find('.qq-progress-bar').attr('aria-valuenow', file.percent)
  fileItem.find('.qq-progress-bar').width(file.percent + '%')
  fileItem.find('.qq-upload-cancel').on('click', function() {
    up.stop()
    up.removeFile(file)
  })
};

var ossUploadFailed = function(up, file, info) {
  var message = info ? info : "Upload Failed"
  var fileItem = $('li#' + file.id)
  fileItem.addClass('qq-upload-fail')
  fileItem.find('.qq-progress-bar-container').hide()
  fileItem.find('.qq-upload-spinner').hide()
  fileItem.find('.qq-upload-status-text').text(message)
  fileItem.find('.qq-upload-cancel').on('click', function() {
    up.removeFile(file)
  })
  $(".form-save button").attr("disabled", false)
};

var ossUploadSuccess = function(up, file) {
  var fileItem = $('li#' + file.id)
  fileItem.addClass('qq-upload-success')
  fileItem.find('.qq-upload-cancel').hide()
  fileItem.find('.qq-progress-bar-container').hide()
  fileItem.find('.qq-upload-spinner').hide()
  fileItem.find('.qq-upload-delete').show()
  fileItem.find('.qq-upload-size').text(plupload.formatSize(file.size))
  fileItem.find('.qq-upload-delete').on('click', function() {
    up.removeFile(file)
  })
  $(".form-save button").attr("disabled", false)
};

