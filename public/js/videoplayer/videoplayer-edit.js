$(document).ready(function(){
    if (typeof videoStreamUrl !== 'undefined' && videoStreamUrl !== null && videoStreamUrl !== '') {
        console.log(videoStreamUrl);

        delete $.ajaxSettings.headers["X-CSRF-TOKEN"];

        $.ajax({
            headers: {},
            method: 'GET',
            url: videoStreamUrl,
            success: function (videoStreamResponse){
                //enable X-CSRF-TOKEN for hungama api call only
                $.ajaxSettings.headers["X-CSRF-TOKEN"] = params.csrfToken;

                // videoStreamResponse = videoStreamResponse.replace('http','https');
                loadVideo(entry_id, prop_id, videoStreamResponse);
            },
            error: function(xhr, type){
                //enable X-CSRF-TOKEN for hungama api call only
                $.ajaxSettings.headers["X-CSRF-TOKEN"] = params.csrfToken;
                
                console.error(xhr);
            }
        });
    } else {
        console.log('not hungama');
        loadVideo(entry_id, prop_id, null);
    }


    function loadVideo(entryId, propId, videoUrl)
    {
        if (typeof setPlayerType !== 'undefined') {
            var playerType = setPlayerType;
        }

        $.ajax({
            method: 'POST',
            url:'/api/get_player',
            data: {entry_id : entryId, prop_id : propId, video_url: videoUrl, player_type : playerType},
            dataType: "html",
            success: function( data ) {
                $("#video").html(data);
                $('#modal-video'+entry_id).removeClass();
                $('.modal-dialog').removeClass();
                $('.modal-content').removeClass();
                $('.modal-header').html('');
                $('.modal-body').removeClass();
                $('.modal-video').removeClass();
                $('.modal-details').html('');
                $('.embed-responsive').removeClass();


                $('#video'+entry_id).addClass('show');
                $('#video'+entry_id).attr('style','display: block;');
            },
            error: function(xhr, type){
                console.error('search get error!');
            }
        });
    }
});