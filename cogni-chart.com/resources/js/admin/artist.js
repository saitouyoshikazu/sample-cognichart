$('#artistModal').on(
    'show.bs.modal',
    function (event) {
        var artist = $(event.relatedTarget);
        var chartrankingitemIdValue = artist.data('chartrankingitemidvalue');
        var artistPhaseValue = artist.data('artistphasevalue');
        var idValue = artist.data('idvalue');
        var iTunesArtistIdValue = artist.data('itunesartistidvalue');
        var artistNameValue = artist.data('artistnamevalue');
        var artistModal = $(this);
        artistModal.find('.modal-body input#artist_modal_chartrankingitem_id').val(chartrankingitemIdValue);
        artistModal.find('.modal-body input#artist_modal_id').val(idValue);
        artistModal.find('.modal-body input#artist_modal_artist_phase').val(artistPhaseValue);
        artistModal.find('.modal-body input#artist_modal_itunes_artist_id').val(iTunesArtistIdValue);
        artistModal.find('.modal-body input#artist_modal_artist_name').val(artistNameValue);

        var enableButtons = [];
        var disableButtons = [];
        if (!idValue && !artistPhaseValue) {
            enableButtons = ['registerartistbutton'];
            disableButtons = ['modifyartistbutton', 'deleteartistbutton', 'releaseartistbutton', 'rollbackartistbutton'];
        } else if (idValue && artistPhaseValue == 'provisioned') {
            enableButtons = ['modifyartistbutton', 'deleteartistbutton', 'releaseartistbutton'];
            disableButtons = ['registerartistbutton', 'rollbackartistbutton'];
        } else if (idValue && artistPhaseValue == 'released') {
            enableButtons = ['modifyartistbutton', 'rollbackartistbutton'];
            disableButtons = ['registerartistbutton', 'deleteartistbutton', 'releaseartistbutton'];
        } else {
            enableButtons = [];
            disableButtons = ['registerartistbutton', 'modifyartistbutton', 'deleteartistbutton', 'releaseartistbutton', 'rollbackartistbutton'];
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
