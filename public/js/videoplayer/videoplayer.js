function getPlayer(entry_id, prop_id) {
    loadVideo(entry_id, prop_id, null);
}

function closeVideo() {
    $('#modal').html('');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove('');
}

function loadVideo(entryId, propId) {
    $.ajax({
        method: 'GET',
        url: '/manage/'+propId+'/cp/videos/video/'+entryId+'/player',
        dataType: "html",
        success: function (data) {
            $("#modal").html(data);
            $('#modal-video' + entryId).addClass('show');
            $('#modal-video' + entryId).attr('style', 'display: block;');
            $('body').addClass('modal-open');
        },
        error: function (xhr, type) {
            console.error('search get error!');
        }
    });

}