$('#itunesSearchResultModal').on(
    'show.bs.modal',
    function (event) {
        var chartRankingItem = $(event.relatedTarget);
        var chartArtistValue = chartRankingItem.data('chartartistvalue');
        var chartMusicValue = chartRankingItem.data('chartmusicvalue');
        var itunesSearchResultModal = $(this);
        itunesSearchResultModal.find('.modal-body textarea#itunes_search_result_modal_itunes_search_result').val('');
        itunesSearchResultModal.find('.modal-body input#itunes_search_result_modal_chart_artist').val(chartArtistValue);
        itunesSearchResultModal.find('.modal-body input#itunes_search_result_modal_chart_music').val(chartMusicValue);
    }
);
