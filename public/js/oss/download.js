var sendRequest = function(id, serverUrl, callback) {
    $('#download_video_' + id).parent().parent().next().html(sending_request).show();
    $.ajax({
        url: serverUrl,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status == 'success') {
                callback(id, data.video_path, data.video_name);
            } else {
                $('#download_video_' + id).attr('url', serverUrl);
                alert(data.message);
            }
        },
        error: function() {
            $('#download_video_' + id).attr('url', serverUrl);
            alert("error!");
        }
    });
};

var loadFile = function(id, uri, callback) {
    $('#download_video_' + id).parent().parent().next().html(downloading);
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'blob';
    xhr.onload = function() {
        callback(window.URL.createObjectURL(xhr.response));
    }
    xhr.open('GET', uri, true);
    xhr.send();
}

function download(id) {
    var serverUrl = $('#download_video_' + id).attr('url');
    if (!serverUrl) {
        alert(no_download_url);
        return;
    }
    $('#download_video_' + id).removeAttr('url');
    sendRequest(id, serverUrl, function (id, fileUrl, fileName) {
        loadFile(id, fileUrl, function(res) {
            $('#download_video_' + id).parent().parent().next().html(saving);
            // 下载完毕回调，URI是blob的资源地址，可以直接调用
            var link = document.createElement('a');
            link.href = res;
            link.setAttribute('download', fileName);
            document.getElementsByTagName("body")[0].appendChild(link);
            // Firefox
            if (document.createEvent) {
                var event = document.createEvent("MouseEvents");
                event.initEvent("click", true, true);
                link.dispatchEvent(event);
            }
            // IE
            else if (link.click) {
                link.click();
            }
            link.parentNode.removeChild(link);
            $('#download_video_' + id).parent().parent().next().html('').hide();
            $('#download_video_' + id).attr('url', serverUrl);
        });
    });
}