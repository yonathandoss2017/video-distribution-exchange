(function($){
    console.log('metadata');
    var metaType = $('input[name=meta_type]');
    var metaDataInput =  $('.metadata-input');
    var metaTypeNone = 1;
    var metaTypeMusic = 2;

    toggleMeta($("input[name=meta_type]:checked").val());

    metaType.on('change', function (){
        toggleMeta($(this).val());
    });

    //Toggle (hide/show) form based on meta type
    function toggleMeta(metaType)
    {
        console.log(metaType);
        console.log([metaType, metaTypeNone]);
        if (metaType == metaTypeNone) {
            metaDataInput.addClass('hidden');
        }

        if (metaType ==  metaTypeMusic) {
            metaDataInput.removeClass('hidden');
        }

    }


}(jQuery));
