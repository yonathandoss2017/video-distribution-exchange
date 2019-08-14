/**
 *
 * Created by jiangdongbin on 27/07/2017.
 */

function bindImageFileChange() {
    $('.imagefile').change(function () {
        var preview_image = $(this).parent().parent().find('.preview_image');
        if (this.files && this.files[0]) {
            $('.error_box').empty();
            convertImageToString(this.files[0], preview_image, function() {
                $(this).parent().find("input").val("");
            });
        }
        if (preview_image.attr('src') !== '') {
            preview_image.show();
        }
    });
}

function convertImageToString(blob, preview_image, failHandler) {
    var reader = new FileReader();
    reader.onload = function (e) {
        if (e.target.result.substr(0, 10) == "data:image") {
            preview_image.attr('src', e.target.result);
            preview_image.slideDown();
            $('input[name=image_ok]').val('1');
        }
        else {
            alert("Invalid image file");
        }
    };
    reader.readAsDataURL(blob);
}

