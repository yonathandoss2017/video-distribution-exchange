var imgUploader = $('#img-s3').fineUploaderS3({
    debug: true,
    template: 'qq-template-s3-img',
    Conditions: ['starts-with', '$x-amz-date', ''],
    request: {
        endpoint: endpointReq,
        accessKey: keyUploader,
        clockDrift: new Date($.now()) - Date.now(),
        success_action_status: "200",
        forceMultipart: true
    },
    objectProperties: {
        region: regionObj,
        key: function (fileId) {
            var filename = this.getName(fileId);
            var uuid = this.getUuid(fileId);
            var ext = filename.substr(filename.lastIndexOf('.') + 1);

            return pathObj + uuid + '.' + ext;
        }
    },
    signature: {
        endpoint: urlEndpoint,
        version: 4,
        params: {
            drift: (new Date($.now()) * 1000) - Date.now()
        }
    },
    uploadSuccess: {
        endpoint: urlEndpoint,
        params: {
            isBrowserPreviewCapable: qq.supportedFeatures.imagePreviews
        }
    },
    iframeSupport: {
        localBlankPagePath: "/s3.fine-uploader/templates/success.html"
    },
    cors: {
        expected: true
    },
    retry:{enableAuto: true, autoAttemptDelay:5, maxAutoAttempts:2},
    chunking: {
        enabled: true,
        concurrent: {
            enabled: true
        }
    },
    resume: {
        enabled: true
    },
    deleteFile: {
        enabled: true,
        method: "POST",
        endpoint: urlDeleteObj
    },
    validation: {
        itemLimit: limitObj,
        sizeLimit: 10000000,
        allowedExtensions: extensions
    },
    thumbnails: {
        placeholders: {
            notAvailablePath: "/s3.fine-uploader/placeholders/not_available-generic_img.png",
            waitingPath: "/s3.fine-uploader/placeholders/waiting-generic.png"
        }
    },
    callbacks: {
        onProgress:function(id, name){
            $('#select-image').hide();
        },
        onComplete: function(id, name, response) {
            $('#directUpload').val('');
            var previewLink = qq(this.getItemByFileId(id)).getByClass('preview-link')[0];

            var filename = this.getName(id);
            var uuid = this.getUuid(id);
            var ext = filename.substr(filename.lastIndexOf('.') + 1);
            imgPath = pathObj + uuid + '.' + ext;
            urlFile = endpointReq + imgPath;

            if (response.success) {
                previewLink.setAttribute("href", urlFile);
                $('.thumbnailUrl-s3').val(urlFile);
                $('#keyNameObj').val(filename+ '.' + ext);
                if ($('.error_img_s3').is(":visible")){
                    $('.error_img_s3').hide();
                }
                
                // additional call another event
                if (additionalEvent) {
                    if (removeImageBox) {
                        $('.qq-file-id-'+id).remove();
                    }
                    saveImage(imgPath, filename);
                }
            }
        },
        onDeleteComplete: function(id, xhr, isError) {
            $('#keyNameObj').val('');
            $('.thumbnailUrl-s3').val('');

            var filename = this.getName(id);
            var uuid = this.getUuid(id);
            var ext = filename.substr(filename.lastIndexOf('.') + 1);
            imgPath = pathObj + uuid + '.' + ext;

            // additional call another event
            if (additionalEvent) {
                deleteImage(propertyId, imgPath);
            }
        },
        onCancel: function(id, xhr, isError) {
            $('#keyNameObj').val('');
            $('.thumbnailUrl-s3').val('');
        },
    }
});