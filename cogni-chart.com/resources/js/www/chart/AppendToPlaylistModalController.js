import Playlist from "../playlist/Playlist"

export default class AppendToPlaylistModalController
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
                    "#append-to-playlist-modal",
                    function (event) {
                        let selected = $(event.relatedTarget);

                        $("#selectedartistidvalue").val(selected.attr("data-artistidvalue"));
                        $("#selectedmusicidvalue").val(selected.attr("data-musicidvalue"));
                        $("#selectedartistnamevalue").val(selected.attr("data-artistnamevalue"));
                        $("#selectedmusictitlevalue").val(selected.attr("data-musictitlevalue"));
                        $("#selectedpromotionvideourlvalue").val(selected.attr("data-promotionvideourlvalue"));
                        $("#selecteditunesbaseurlvalue").val(selected.attr("data-itunesbaseurlvalue"));
                        self.loadPlaylistAppended();
                    }
                );

                $(document).on(
                    "click",
                    "#create-instant-playlist",
                    function (event) {
                        self.appendInstantPlaylists();
                    }
                );

                $(document).on(
                    "click",
                    ".selected-playlist-name-label",
                    function (event) {
                        let playlistNameValue = $(this).attr("data-playlistname");
                        self.appendPlaylistItem(playlistNameValue);
                    }
                );
            }
        );
    }

    appendPlaylistAppendedDom(playlistName)
    {
        let liPlaylist = $('<li/>', {class: 'list-group-item playlist'});
        let playlistRow = $('<div/>', {class: 'row'})
            .append(
                $('<a/>', {class: 'selected-playlist-name-label word-wrap-break-word col-12'}).text(playlistName).attr('data-playlistname', playlistName)
            );
        liPlaylist.append(playlistRow);
        $(document).find('ul#playlists-appended').append(liPlaylist);
    }

    loadPlaylistAppended()
    {
        $(document).find('ul#playlists-appended').find('li').each(
            function () {
                $(this).remove();
            }
        );
        let playlistNames = this.playlist.list();
        for (let index in playlistNames) {
            this.appendPlaylistAppendedDom(playlistNames[index]);
        }
    }

    appendInstantPlaylists()
    {
        let result = this.playlist.append($('#instant-playlist-name').val());
        if (!result) {
            return false;
        }
        this.appendPlaylistAppendedDom(result);
        return true;
    }

    appendPlaylistItem(playlistNameValue)
    {
        let artistIdValue = $("#selectedartistidvalue").val();
        let musicIdValue = $("#selectedmusicidvalue").val();
        let artistNameValue = $("#selectedartistnamevalue").val();
        let musicTitleValue = $("#selectedmusictitlevalue").val();
        let youtubeIdValue = $("#selectedpromotionvideourlvalue").val();
        let iTunesBaseUrlValue = $("#selecteditunesbaseurlvalue").val()
        if (!artistNameValue || !musicTitleValue || !artistIdValue || !musicIdValue) {
            $('#append-to-playlist-modal').modal('hide');
            return;
        }
        if (this.playlist.appendPlaylistItem(playlistNameValue, artistIdValue, musicIdValue, artistNameValue, musicTitleValue, youtubeIdValue, iTunesBaseUrlValue)) {
            $('#append-to-playlist-modal').modal('hide');
        }
    }

}
