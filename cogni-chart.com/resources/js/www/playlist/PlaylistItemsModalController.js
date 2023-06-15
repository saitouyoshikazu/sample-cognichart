import {player, showPlayer, hidePlayer, cue} from "../player/player";
import Playlist from "./Playlist";
import Sortable from "sortablejs";

export default class PlaylistItemsModalController
{

    constructor()
    {
        this.playlist = new Playlist();

        let self = this;

        this.sortable = new Sortable(
            document.getElementById("playlistitems"),
            {
                direction: "vertical",
                onUpdate: function (event) {
                    self.rebuildPlaylistItems();
                },
                disabled: false,
                delay: 100
            }
        );

        $(window).on(
            'load',
            function () {
                $('#playlistitems-modal').on(
                    'show.bs.modal',
                    function (event) {
                        $('#playlist-modal').modal('hide');
                        self.usageRestriction();
                        let fired = $(event.relatedTarget);
                        let playlistNameValue = fired.attr('data-playlistnamevalue');
                        $('#export-playlist-button').attr('data-playlistnamevalue', playlistNameValue);
                        $('#export-playlist-button').data('playlistnamevalue', playlistNameValue);
                        $('#reset-promotion-videos-button').attr('data-playlistnamevalue', playlistNameValue);
                        $('#reset-promotion-videos-button').data('playlistnamevalue', playlistNameValue);
                        self.loadPlaylistItems(playlistNameValue);
                    }
                );

                $('#playlistitems-modal').on(
                    'shown.bs.modal',
                    function (event) {
                        $('body').addClass('modal-open');
                    }
                );

                $('#play-playlist').on(
                    'click',
                    function (event) {
                        self.playPlaylist();
                    }
                );

                $('#export-playlist-button').on(
                    'click',
                    function (event) {
                        self.exportPlaylist($(this).attr('data-playlistnamevalue'));
                    }
                );

                $('#reset-promotion-videos-button').on(
                    'click',
                    function (event) {
                        let playlistNameValue = $(event.currentTarget).attr('data-playlistnamevalue');
                        $('#reset-promotion-videos-button').button('loading');
                        self.resetPromotionVideos(playlistNameValue);
                    }
                );

                $(document).on(
                    'click',
                    '.playlistitem-delete',
                    function (event) {
                        let liObj = $(event.currentTarget).parents('li');
                        self.removePlaylistItem(liObj);
                    }
                );

            }
        );
    }

    usageRestriction()
    {
        let ua = navigator.userAgent;
        if (ua.indexOf('iPhone')  > 0 ||
            ua.indexOf('Android') > 0 ||
            ua.indexOf('iPad')    > 0 ||
            ua.indexOf('iPod')    > 0 ||
            ua.indexOf('Mobile')  > 0
        ) {
            $('#export-playlist-button').prop('disabled', true);
            $('#export-playlist-button').hide();
        } else {
            $('#export-playlist-button').prop('disabled', false);
            $('#export-playlist-button').show();
        }
    }

    loadPlaylistItems(playlistNameValue)
    {
        $('#playlistitems > li').each(
            function () {
                $(this).remove();
            }
        );
        let iTunesToken = $('#playlistitems').attr("data-ittoken");
        let imgAppleMusic = $(
            '<img/>',
            {
                "src": "https://linkmaker.itunes.apple.com/en-us/badge-lrg.svg?kind=song&bubble=apple_music",
                "alt": "Listen on Apple Music"
            }
        );
        let imgITunesStore = $(
            '<img/>',
            {
                "src": "https://linkmaker.itunes.apple.com/en-us/badge-lrg.svg?kind=song&bubble=itunes_music",
                "alt": "Buy on iTunes Store"
            }
        );
        $('#playlistitems-modal-label').text(playlistNameValue);
        $('#play-playlist').attr('data-playlistnamevalue', playlistNameValue);
        $('#playlistitems').attr('data-playlistnamevalue', playlistNameValue);
        let items = this.playlist.items(playlistNameValue);
        if (items === null) {
            return;
        }
        for (let index in items) {
            let artistIdValue = "";
            if (items[index]['artistId']) {
                artistIdValue = items[index]['artistId'];
            }
            let musicIdValue = "";
            if (items[index]['musicId']) {
                musicIdValue = items[index]['musicId'];
            }
            let iTunesBaseUrlValue = "";
            if (items[index]['iTunesBaseUrl']) {
                iTunesBaseUrlValue = items[index]['iTunesBaseUrl'];
            }
            let liDom = $('<li/>', {class: 'list-group-item'})
                .attr('data-playlistnamevalue', playlistNameValue)
                .attr('data-indexvalue', index)
                .attr('data-artistidvalue', artistIdValue)
                .attr('data-musicidvalue', musicIdValue)
                .attr('data-artistnamevalue', items[index]['artistName'])
                .attr('data-musictitlevalue', items[index]['musicTitle'])
                .attr('data-youtubeidvalue', items[index]['youtubeId'])
                .attr('data-itunesbaseurlvalue', iTunesBaseUrlValue);

            let divPlaylistItemRow = $('<div/>', {class: 'row'});
            let divArtistNameDom = $('<div/>', {class: 'col-5 pl-0'}).text(items[index]['artistName']);
            let divMusicTitleDom = $('<div/>', {class: 'col-5 pl-0'}).text(items[index]['musicTitle']);
            let divDeleteButtonDom = $('<div/>', {class: 'col-2 pl-0 text-center'})
                .append(
                    $('<button/>', {type: 'button', class: 'btn btn-sm btn-outline-danger transform-center playlistitem-delete'})
                    .append($('<i/>', {class: 'fas fa-trash-alt'}))
                );
            divPlaylistItemRow.append(divMusicTitleDom).append(divArtistNameDom).append(divDeleteButtonDom);
            liDom.append(divPlaylistItemRow);

            if (iTunesBaseUrlValue) {
                let divMusicAffiliateRow = $('<div/>', {class: 'row'});
                let amLink = iTunesBaseUrlValue + "&mt=1&app=music&at=" + iTunesToken;
                divMusicAffiliateRow.append($('<a/>', {href: amLink, class: 'af-button', rel: 'nofollow sponsored', target: '_blank'}).append(imgAppleMusic.clone()));
                let itLink = iTunesBaseUrlValue + "&mt=1&app=itunes&at=" + iTunesToken;
                divMusicAffiliateRow.append($('<a/>', {href: itLink, class: 'af-button', rel: 'nofollow sponsored', target: '_blank'}).append(imgITunesStore.clone()));
                liDom.append(divMusicAffiliateRow);
            }

            $('#playlistitems').append(liDom);
        }
    }

    playPlaylist()
    {
        let playlistNameValue = $('#play-playlist').attr('data-playlistnamevalue');
        cue(playlistNameValue);
        showPlayer();
        $('#playlistitems-modal').modal('hide');
        let items = this.playlist.items(playlistNameValue);
        if (items.length > 200) {
            alert("Youtube player can only set up to 200 songs.");
        }
    }

    exportPlaylist(playlistNameValue)
    {
        let playlist = this.playlist.get(playlistNameValue);
        if (!playlist) {
            alert("Couldn't find playlist " + playlistNameValue + ".");
            return;
        }

        let blobObject = null;
        if (window.Blob) {
            blobObject = new Blob([JSON.stringify(playlist)], {type: "application/JSON"});
        } else if (window.BlobBuilder) {
            let builder = new BlobBuilder();
            builder.append(JSON.stringify(playlist));
            blobObject = builder.getBlob("application/JSON");
        } else {
            alert("Your web browser doesn't support file exporting, sorry.");
            return;
        }

        if (window.navigator.msSaveBlob) {
            window.navigator.msSaveBlob(blobObject, playlist['name']+".json");
        } else {
            let urlObject = null;
            if (window.URL) {
                urlObject = window.URL;
            } else if (window.webkitURL) {
                urlObject = window.webkitURL;
            }
            if (urlObject === null) {
                alert("Your web browser doesn't support file exporting, sorry.");
                return;
            }
            $('#export-playlist-button').attr('href', urlObject.createObjectURL(blobObject)).attr('download', playlist['name']+".json");
        }
    }

    removePlaylistItem(removeObj)
    {
        let playlistNameValue = removeObj.data('playlistnamevalue');
        let indexValue = removeObj.data('indexvalue');
        let youtubeIdValue = removeObj.data('youtubeidvalue');

        this.playlist.removePlaylistItem(playlistNameValue, indexValue, youtubeIdValue);
        this.loadPlaylistItems(playlistNameValue)
    }

    rebuildPlaylistItems()
    {
        let playlistNameValue = $('#playlistitems').data('playlistnamevalue');
        let playlistItems = new Array();
        $('#playlistitems > li').each(
            function () {
                playlistItems.push({
                    'artistId': $(this).attr('data-artistidvalue'),
                    'musicId': $(this).attr('data-musicidvalue'),
                    'artistName': $(this).attr('data-artistnamevalue'),
                    'musicTitle': $(this).attr('data-musictitlevalue'),
                    'youtubeId' : $(this).attr('data-youtubeidvalue'),
                    'iTunesBaseUrl': $(this).attr('data-itunesbaseurlvalue')
                });
            }
        );
        this.playlist.rebuildPlaylistItems(playlistNameValue, playlistItems);
        this.loadPlaylistItems(playlistNameValue);
    }

    resetPromotionVideos(playlistNameValue)
    {
        let items = this.playlist.items(playlistNameValue);
        let postParams = {'music_ids': new Array()};
        let paramCount = 0;
        let startIndex = 0;
        for (let index in items) {
            if (paramCount % 1000 == 0) {
                startIndex = index;
                postParams['music_ids'] = new Array();
            }
            paramCount++;
            if (items[index]['musicId'] && items[index]['musicId'].length > 0) {
                postParams['music_ids'].push(items[index]['musicId']);
            }
            if (paramCount % 1000 == 0) {
                this.executeResetPromotionVideos(playlistNameValue, startIndex, paramCount-1, postParams);
            }
        }
        this.executeResetPromotionVideos(playlistNameValue, startIndex, paramCount-1, postParams);
    }

    async executeResetPromotionVideos(playlistNameValue, startIndex, endIndex, postParams)
    {
        try {
            let items = this.playlist.items(playlistNameValue);
            if (!items) {
                $('#reset-promotion-videos-button').button('reset');
                return;
            }
            if (postParams['music_ids'].length > 0) {
                let response = await axios.post('/music/resetpromotionvideos', postParams);
                let result = response.data;
                let error = result['error'];
                let musics = result['musics'];
                if (error && Object.keys(error).length > 0) {
                    alert(error);
                    return;
                }
                if (musics && Object.keys(musics).length > 0) {
                    let index = startIndex;
                    while (index <= endIndex) {
                        if (items[index]['musicId'] && items[index]['musicId'].length > 0) {
                            let promotionVideoUrl = musics[items[index]['musicId']]['youtubeId'];
                            items[index]['youtubeId'] = promotionVideoUrl;

                            let iTunesBaseUrlValue = musics[items[index]['musicId']]['iTunesBaseUrl'];
                            items[index]['iTunesBaseUrl'] = iTunesBaseUrlValue;
                        }
                        index++;
                    }
                    this.playlist.rebuildPlaylistItems(playlistNameValue, items);
                    if (items.length - 1 == endIndex) {
                        alert('Promotion videos of ' + playlistNameValue + ' was reset to data that is managed by Cogni Chart.');
                    }
                }
            }
            if (items.length - 1 == endIndex) {
                $('#reset-promotion-videos-button').button('reset');
                this.loadPlaylistItems(playlistNameValue);
            }
        } catch (e) {
            alert(e);
        }
    }

}
