(function($){
    $.fn.focusTextToEnd = function(){
        this.focus();
        var $thisVal = this.val();
        this.val('').val($thisVal);
        return this;
    }
}(jQuery));

$(document).ready(function(){
    $('#keyNameObj').val('');
    $('#uuidObj').val('');
    $('#filenameObj').val('');
    $('#extObj').val('');

    thumbnailUrl = $('.thumbnailUrl-s3').val();
    directUpload = $('#directUpload').val();
    $('.vidtitle').focus();

    if ($('.upload_url').is(':checked')) {
        $('.url').toggle();
    }
    if ($('.http_upload').is(':checked')) {
        $('.direct-upload').toggle();
    }
    if (thumbnailUrl) {
        $('#select-image').hide();
        $("#img-upload").prop('checked', true);
    }
    if (directUpload) {
        $('.qq-upload-button').hide();
        $("#direct_s3").prop('checked', true);
    }

    methodImage();
    methodVideo();

    $('.img-method').click(function(){
        methodImage();
        hideAlertDanger();
    });
    $('.video_method').click(function(){
        methodVideo();
        hideAlertDanger();
        removeErrorVideo();
    });

    function hideAlertDanger(){
        if ($('.alert-danger').is(':visible')) {
            $('.alert-danger').hide();
        }
    }

    function methodVideo(){
        if ($('#direct_s3').is(':checked')) {
            $('.video_url').val('');
            if (!$('.direct-upload').is(":visible")) {
                $('.direct-upload').toggle();
            }
        }
        if ($('#s3_url').is(':checked')) {
            if (!$('.url').is(":visible")) {
                $('.url').toggle();
            }
            $('input.video_url').focusTextToEnd();
        }
    }

    function methodImage(){
        if ($('#img-upload').is(':checked')) {
            $('#img_url_file').prop('required',false);
            $('.img-direct-upload-container').show();
            $('#img_url_file').val('');
            if ($('.img-url-upload').is(":visible")){
                $('.img-url-upload').hide();
            }
            removeErrorImage();
        }

        if ($('#img-url').is(':checked')) {
            $('#img_url_file').prop('required',true);
            $('.img-url-upload').show();
            if($('.img-direct-upload-container').is(":visible")){
                $('.img-direct-upload-container').hide();
            }
            removeErrorImage();
        }

        if ($('#img-auto').is(':checked')) {
            $('#img_url_file').prop('required',false);
            $('.img-url-upload').hide();
            $('.img-direct-upload-container').hide();
            removeErrorImage();
        }

    }

    $('#img_url_file').keyup(function(){
        if ($('#error_img_url').is(':visible')) {
            $('#error_img_url').toggle();
        }
        if ($('.img-url-upload').hasClass('has-danger')) {
            $('.img-url-upload').removeClass('has-danger');
        }
        removeErrorImage();
    });

    function removeErrorImage(){
        if ($('#error_img_method').is(':visible')) {
            $('#error_img_method').toggle();
            $('#error_img_method').removeClass('has-danger');
        }
    }

    function removeErrorVideo(){
        if ($('#error_video_upload').is(':visible')) {
            $('#error_video_upload').toggle();
            $('#error_video_upload').removeClass('has-danger');
        }
        if ($('#error_url_video').is(':visible')) {
            $('#error_url_video').toggle();
            $('#error_url_video').removeClass('has-danger');
        }
    }
});