
function getCommonPlaylist(playlist_id, entry_id, divWidth){
    $.get('/marketplace/get-playlists?playlist_id='+playlist_id+'&entries='+entry_id, function(playlists){
        data = playlists.dataPlaylist;
        relatedPlaylist = [];
        
        if (0 < data.length) {
            $("#otherPlaylistCt").removeClass('hide');

            data.forEach(function(p) {
                created_at = convertToTimestamp(p.created_at);
                imagePl = "/serve/image/"+p.property_id+"/playlist/"+p.id+"/"+updated_at+"?width=510";
                relatedPlaylist.push(''+
                        '<div class="col-md-'+divWidth+' playlist-main">'+
                            '<section>'+
                                '<div class="video-img main img-parent">'+
                                    '<a href="/marketplace/playlist/'+p.id+'" class="img-container">'+
                                        '<img src="'+imagePl+'" alt="'+p.name+'" title="'+p.name+'">'+
                                    '</a>'+
                                    '<div class="playlist-detail">'+
                                        '<div class="playlist-number">'+
                                            '<span class="amount-numbers">'+p.entries_count+'</span><br>Videos'+
                                        '</div>'+
                                        '<div class="playlist-icon">'+
                                            '<img src="/images/marketplace/playlist.svg" alt="">'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<label class="playlist-label">playlist</label>'+
                                '<div class="info">'+
                                    '<div class="video-title">'+
                                        '<a href="/marketplace/playlist/'+p.id+'" title="'+p.name+'">'+p.name+'</a>'+
                                    '</div>'+
                                    '<div class="small-detail">'+p.content_provider.name+'</div>'+
                                '</div>'+
                            '</section>'+
                        '</div>'
                    );

                $('#otherPlaylist').html(relatedPlaylist); console.log(relatedPlaylist);
            }); 
        } 
    });
}

function convertToTimestamp(dateTime){
    var time = moment(dateTime);
    return time.format("x");                    
}
