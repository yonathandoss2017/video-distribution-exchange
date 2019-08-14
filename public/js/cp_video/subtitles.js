subtitles = [];
$('#add-subtitle').click(function(e){
    e.preventDefault();
    labelSelected = $(".select-sub option:selected").text();
    subCode = $(".select-sub").val();

    if(subCode && subCode !== '-' && ($.inArray( subCode,  subtitles) == -1)){
        subtitles.push(subCode);

        $('#subtitles').append(''+
            '<div id="'+subCode+'" class="form-group row">'+
                '<label class="col-md-3 control-label" subtitle-code="'+subCode+'">'+labelSelected+' *</label>'+
                '<div class="col-md-9 d-flex justify-content-between" subtitle-code="'+subCode+'">'+
                    '<div class="box-custom-file width-406">'+
                        '<input type="file" name="'+subCode+'" id="'+subCode+'_1" class="file-input" class="inputfile" required/>'+
                        '<label for="'+subCode+'_1" id="input-file" class="btn-file-input"><div>' + $('#add-subtitle-browse-lang').val() + '</div></label>'+
                    '</div>'+
                    '<div class="extra-delete-button" subtitle-code="'+subCode+'">'+
                        '<a href="#" id="delete-sub" subtitle-code="'+subCode+'" class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></a>'+
                    '</div>'+
                '</div>'+

            '</div>'
        );

        $(".select-sub option:selected").removeAttr("selected");
        $('.list-subs').val(subtitles);
        $('.select-sub').val('-');
    }else{
        if (! subCode || subCode === '-') {
            alert("Please select a languange");
        } else {
            alert('Subtitle '+labelSelected+' already selected!')
        }
    }
});

$("#subtitles").on("click", "a#delete-sub", function(e){
    e.preventDefault();
    subCode = $(this).attr('subtitle-code');

    if($.inArray( subCode,  subtitles) != -1){
        subtitles.splice( $.inArray(subCode, subtitles), 1 );
        $('.list-subs').val(subtitles);
    }

    $('#'+ subCode).remove();
});

var btnDeleteCaption = $('.btn-delete-caption');
var deletedCaptions = $('input[name=deleted_captions]');
btnDeleteCaption.on('click', function (e){
    var captionLang = $(this).attr('lang');
    var deletedCaptionsValue = '';
    if (deletedCaptions.val() != '') {
         deletedCaptionsValue = deletedCaptions.val() + ',' + captionLang;
    } else {
        deletedCaptionsValue = captionLang;
    }
    deletedCaptions.val(deletedCaptionsValue);
    console.log(deletedCaptions.val());

    $('#caption_'+ captionLang).remove();
    console.log('caption ' + captionLang + ' deleted');
});