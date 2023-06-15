import Playlist from "../playlist/Playlist"

export default class AppendAllSongsModalController
{

    constructor()
    {
        this.playlist = new Playlist();

        let self = this;
        $(window).on(
            "load",
            function (event) {
                $(document).on(
                    "show.bs.modal",
                    "#append-all-songs-modal",
                    function (event) {
                        self.loadAppendAllSongsPlaylist();
                    }
                );

                $(document).on(
                    "click",
                    "#create-append-all-songs-instant-playlist",
                    function (event) {
                        self.appendAllSongsInstantPlaylists();
                    }
                );

                $(document).on(
                    "click",
                    ".append-all-songs-selected-playlist-name-label",
                    function (event) {
                        let playlistNameValue = $(this).attr("data-playlistname");
                        self.appendAllSongsPlaylistItem(playlistNameValue);
                    }
                );
            }
        );
    }

    appendToAppendAllSongsPlaylistsDom(playlistName)
    {
        let liPlaylist = $('<li/>', {class: 'list-group-item playlist'});
        let playlistRow = $('<div/>', {class: 'row'})
            .append(
                $('<a/>', {class: 'append-all-songs-selected-playlist-name-label word-wrap-break-word'}).text(playlistName).attr('data-playlistname', playlistName)
            );
        liPlaylist.append(playlistRow);
        $(document).find('ul#append-all-songs-playlists').append(liPlaylist);
    }

    loadAppendAllSongsPlaylist()
    {
        $(document).find('ul#append-all-songs-playlists').find('li').each(
            function () {
                $(this).remove();
            }
        );
        let playlistNames = this.playlist.list();
        for (let index in playlistNames) {
            this.appendToAppendAllSongsPlaylistsDom(playlistNames[index]);
        }
    }

    appendAllSongsInstantPlaylists()
    {
        let result = this.playlist.append($('#append-all-songs-instant-playlist-name').val());
        if (!result) {
            return false;
        }
        this.appendToAppendAllSongsPlaylistsDom(result);
        return true;
    }

    appendAllSongsPlaylistItem(playlistNameValue)
    {
        let allSongs = new Array();
        $('#wwwcontentpart').find('.pv-data-link').each(
            function (event) {
                let artistIdValue = $(this).attr("data-artistidvalue");
                let musicIdValue = $(this).attr("data-musicidvalue");
                let artistNameValue = $(this).attr("data-artistnamevalue");
                let musicTitleValue = $(this).attr("data-musictitlevalue");
                let youtubeIdValue = $(this).attr("data-promotionvideourlvalue");
                let iTunesBaseUrlValue = $(this).attr("data-itunesbaseurlvalue");
                if (!artistNameValue || !musicTitleValue || !artistIdValue || !musicIdValue) {
                    return true;
                }
                allSongs.push(
                    {
                        'artistId': artistIdValue,
                        'musicId': musicIdValue,
                        'artistName': artistNameValue,
                        'musicTitle': musicTitleValue,
                        'youtubeId': youtubeIdValue,
                        'iTunesBaseUrl': iTunesBaseUrlValue
                    }
                );
            }
        );
        let items = this.playlist.items(playlistNameValue);
        let totalCount = allSongs.length;
        if (items) {
            totalCount += items.length;
        }
        if (totalCount > 200) {
            alert("You can only add up to 200 songs in a playlist.");
            return;
        }
        for (let index in allSongs) {
            this.playlist.appendPlaylistItem(
                playlistNameValue,
                allSongs[index]['artistId'],
                allSongs[index]['musicId'],
                allSongs[index]['artistName'],
                allSongs[index]['musicTitle'],
                allSongs[index]['youtubeId'],
                allSongs[index]['iTunesBaseUrl']
            );
        }
        $('#append-all-songs-modal').modal('hide');
    }

}
