
function getRelatedVid(key, limitResult, endpointRequest){
    $.ajax({
            headers: {},
            method: 'GET',
            data: {keywords: key, limit: limitResult, filter: 'video'},
            url: endpointRequest,
            success: function (res){
                data = $.parseJSON(res);
                numFound = data.response.numFound;
                related = data.response.docs;
                relatedVid = [];
                related.forEach(function(e){
                    if (e.ivx_id !== entry_id) {
                        image = "/serve/image/"+e.property_id+"/"+e.type+"/"+e.ivx_id+"/"+e.updated_at+"?width=600";
                        
                        relatedVid.push(''+
                                '<div class="single-right-grids col-md-12">'+
                                    '<div class="col-md-5 single-right-grid-left ">'+
                                        '<div class="img-parent">'+
                                            '<a href="/marketplace/'+e.type+'/'+e.ivx_id+'" class="img-container">'+
                                                '<img src="'+image+'" alt="'+e.title[0]+'" >'+
                                            '</a>'+
                                        '</div>'+
                                        '<div class="time"><p>'+convertTimeToString(e.entry_duration)+'</p></div>'+
                                    '</div>'+
                                    '<div class="col-md-7 single-right-grid-right">'+
                                        '<div><a href="/marketplace/'+e.type+'/'+e.ivx_id+'" class="title">'+e.title[0]+'</a></div>'+
                                        '<span class="info-small">'+e.property_name[0]+'</span>'+
                                    '</div>'+
                                    '<div class="clearfix"></div>'+
                                '</div>'
                            );

                        $('#relatedvid').html(relatedVid);                   
                    }
                });

                if (numFound <= limitResult) {
                    $('.load').hide();
                }
            },
            error: function(xhr, type){
                $.ajaxSettings.headers["X-CSRF-TOKEN"] = params.csrfToken;
                alert('ERROR');
                console.error(xhr);
            }
        });
}

function convertTimeToString(durationVar) {
    var duration = moment.unix(durationVar);
    return duration.format("mm:ss");                
}