$(function () {
    $('.lazypromotionvideo').each(function () {
        $(this).one('inview', function (event, isInview) {
            if (isInview) {
                var idValue = $(this).attr('id');
                var videoIdValue = $(this).data('promotionvideoid');
                new YT.Player(idValue, {
                    videoId: videoIdValue,
                    width: 480,
                    height: 270,
                    origin: location.protocol + "//" + location.hostname + "/"
                });
            }
        })
    });
});

$('#musicModal').on(
    'show.bs.modal',
    function (event) {
        var music = $(event.relatedTarget);
        var chartrankingitemIdValue = music.data('chartrankingitemidvalue');
        var musicPhaseValue = music.data('musicphasevalue');
        var idValue = music.data('idvalue');
        var iTunesArtistIdValue = music.data('itunesartistidvalue');
        var musicTitleValue = music.data('musictitlevalue');
        var promotionVideoUrlValue = music.data('promotionvideourlvalue');
        var thumbnailUrlValue = music.data('thumbnailurlvalue');
        var musicModal = $(this);
        musicModal.find('.modal-body input#music_modal_chartrankingitem_id').val(chartrankingitemIdValue);
        musicModal.find('.modal-body input#music_modal_id').val(idValue);
        musicModal.find('.modal-body input#music_modal_music_phase').val(musicPhaseValue);
        musicModal.find('.modal-body input#music_modal_itunes_artist_id').val(iTunesArtistIdValue);
        musicModal.find('.modal-body input#music_modal_music_title').val(musicTitleValue);
        musicModal.find('.modal-body input#music_modal_promotion_video_url').val(promotionVideoUrlValue);
        musicModal.find('.modal-body input#music_modal_thumbnail_url').val(thumbnailUrlValue);

        var enableButtons = [];
        var disableButtons = [];
        if (!idValue && !musicPhaseValue) {
            enableButtons = ['registermusicbutton'];
            disableButtons = ['modifymusicbutton', 'deletemusicbutton', 'releasemusicbutton', 'rollbackmusicbutton'];
        } else if (idValue && musicPhaseValue == 'provisioned') {
            enableButtons = ['modifymusicbutton', 'deletemusicbutton', 'releasemusicbutton'];
            disableButtons = ['registermusicbutton', 'rollbackmusicbutton'];
        } else if (idValue && musicPhaseValue == 'released') {
            enableButtons = ['modifymusicbutton', 'rollbackmusicbutton'];
            disableButtons = ['registermusicbutton', 'deletemusicbutton', 'releasemusicbutton'];
        } else {
            enableButtons = [];
            disableButtons = ['registermusicbutton', 'modifymusicbutton', 'deletemusicbutton', 'releasemusicbutton', 'rollbackmusicbutton'];
        }
        for (var i = 0; i < enableButtons.length; i++) {
            $('#'+enableButtons[i]).prop('disabled', false);
            $('#'+enableButtons[i]).css('display', 'block');
        }
        for (var i = 0; i < disableButtons.length; i++) {
            $('#'+disableButtons[i]).prop('disabled', true);
            $('#'+disableButtons[i]).css('display', 'none');
        }
    }
);
