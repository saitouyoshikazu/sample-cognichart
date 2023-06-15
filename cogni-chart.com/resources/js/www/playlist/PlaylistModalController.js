import Playlist from "./Playlist";
import Sortable from "sortablejs";

export default class PlaylistModalController
{

    constructor()
    {
        this.playlist = new Playlist();
        this.playlistsDom = $(document).find('ul#playlists');

        let self = this;

        this.sortable = new Sortable(
            document.getElementById("playlists"),
            {
                direction: "vertical",
                onUpdate: function (event)
                {
                    self.rebuild();
                },
                disabled: true,
                delay: 100
            }
        );

        $(window).on(
            'load',
            function (event) {
                $(document).on(
                    "show.bs.modal",
                    "#playlist-modal",
                    function (event) {
                        self.usageRestriction();
                        self.loadPlaylists();
                        self.createPlaylistOff();
                        self.editPlaylistOff();
                    }
                );

                $(document).on(
                    "click",
                    "#create-playlist-on",
                    function (event) {
                        self.editPlaylistOff();
                        self.createPlaylistOn();
                    }
                );

                $(document).on(
                    "click",
                    "#create-new-playlist",
                    function (event) {
                        if (self.appendPlaylists()) {
                            self.createPlaylistOff();
                        }
                    }
                );

                $(document).on(
                    "click",
                    "#cancel-new-playlist",
                    function (event) {
                        self.createPlaylistOff();
                    }
                );

                $(document).on(
                    "click",
                    "#edit-playlist-on",
                    function (event) {
                        self.createPlaylistOff();
                        self.editPlaylistOn();
                    }
                );

                $(document).on(
                    "click",
                    "#edit-playlist-off",
                    function (event) {
                        self.editPlaylistOff();
                    }
                );

                $(document).on(
                    "click",
                    "button.playlist-delete",
                    function (event) {
                        let row = $(event.target).parents('li');
                        self.remove(row);
                    }
                );

                $(document).on(
                    "click",
                    "button.playlist-edit",
                    function (event) {
                        if ($(event.currentTarget).hasClass('editing')) {
                            return;
                        }
                        let row = $(event.target).parents('li');
                        self.editPlaylistNameOn(row);
                    }
                );

                $(document).on(
                    "click",
                    "button.editing",
                    function (event) {
                        let row = $(this).closest('li');
                        if (!self.rename(row)) {
                            return;
                        }
                        self.editPlaylistNameOff(row);
                    }
                );

                $(document).on(
                    "click",
                    "#import-playlist",
                    function (event) {
                        self.loadPlaylists();
                        self.createPlaylistOff();
                        self.editPlaylistOff();
                        $("#import-playlist-field").val("");
                        $("#import-playlist-field").trigger("click");
                    }
                );

                $(document).on(
                    "change",
                    "#import-playlist-field",
                    function (event) {
                        let files = event.target.files;
                        if (!files[0]) {
                            return;
                        }

                        let fileReaderObject = new FileReader();
                        fileReaderObject.onload = function (progressEvent) {
                            self.playlist.parse(progressEvent.target.result);
                            self.loadPlaylists();
                        };
                        fileReaderObject.readAsText(files[0]);
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
            $('#import-playlist').prop('disabled', true);
            $('#import-playlist').hide();
        } else {
            $('#import-playlist').prop('disabled', false);
            $('#import-playlist').show();
        }
    }

    createPlaylistOn()
    {
        $('#create-playlist').removeClass('d-none');
        $('#new-playlist-name').val('');
    }

    createPlaylistOff()
    {
        $('#new-playlist-name').val('');
        $('#create-playlist').addClass('d-none');
    }

    editPlaylistOn()
    {
        let self = this;
        $('ul#playlists > .playlist').each(
            function () {
                $(this).find('.playlist-edit-area').removeClass('d-none');
                $(this).find('.playlist-name-area').removeClass('w-100');
                $(this).find('.playlist-name-area').addClass('w-65');
                $(this).find('a.playlist-name-label').attr('data-target', '');
                self.editPlaylistNameOff($(this));
            }
        );
        $('#edit-playlist-off').removeClass('d-none');
        this.sortable.option("disabled", false);
    }

    editPlaylistOff()
    {
        let self = this;
        $('ul#playlists > .playlist').each(
            function () {
                self.editPlaylistNameOff($(this));
                $(this).find('.playlist-name-area').removeClass('w-65');
                $(this).find('.playlist-name-area').addClass('w-100');
                $(this).find('.playlist-edit-area').addClass('d-none');
                $(this).find('a.playlist-name-label').attr('data-target', '#playlistitems-modal');
            }
        );
        $('#edit-playlist-off').addClass('d-none');
        this.sortable.option("disabled", true);
    }

    editPlaylistNameOn(row)
    {
        let playlistNameLabel = row.find('div.playlist-name-area > a.playlist-name-label');
        let playlistNameInput = $('<input/>', {type: 'text', name: 'playlistname', class: 'playlist-name-input form-control ignore-elements'});
        playlistNameInput.val(playlistNameLabel.text());
        playlistNameInput.attr('data-oldplaylistname', playlistNameLabel.text());
        playlistNameLabel.addClass('d-none');
        row.find('div.playlist-name-area').append(playlistNameInput);

        let playlistEditButton = row.find('button.playlist-edit');
        playlistEditButton.find('i').removeClass('fas fa-edit');
        playlistEditButton.find('i').addClass('far fa-check-circle text-primary');
        playlistEditButton.addClass('editing');

        this.sortbleOnOff();
    }

    editPlaylistNameOff(row)
    {
        let playlistNameLabel = row.find('div.playlist-name-area > a.playlist-name-label');
        let playlistNameInput = row.find('div.playlist-name-area > input.playlist-name-input');
        playlistNameInput.remove();
        playlistNameLabel.removeClass('d-none');

        let playlistEditButton = row.find('button.playlist-edit');
        playlistEditButton.removeClass('editing');
        playlistEditButton.find('i').removeClass('far fa-check-circle text-primary');
        playlistEditButton.find('i').addClass('fas fa-edit');

        this.sortbleOnOff();
    }

    appendPlaylistsDom(playlistName)
    {
        let liPlaylist = $('<li/>', {class: 'list-group-item playlist'});
        let playlistRow = $('<div/>', {class: 'row'});
        let playlistNameArea = $('<div/>', {class: 'playlist-name-area w-100'});
        let playlistEditArea = $('<div/>', {class: 'playlist-edit-area w-35 position-relative d-none'});
        let playlistEditBtns = $('<div/>', {class: 'btn-group transform-center'});

        playlistNameArea
            .append(
                $('<a/>', {class: 'playlist-name-label word-wrap-break-word'})
                .text(playlistName)
                .attr('data-toggle', 'modal')
                .attr('data-target', '#playlistitems-modal')
                .attr('data-playlistnamevalue', playlistName)
            );
        playlistEditBtns
            .append(
                $('<button/>', {type: 'button', class: 'btn btn-outline-info playlist-edit'})
                    .append($('<i/>', {class: 'fas fa-edit'}))
            )
            .append(
                $('<button/>', {type: 'button', class: 'btn btn-outline-danger playlist-delete'})
                    .append($('<i/>', {class: 'fas fa-trash-alt'}))
            );
        playlistEditArea.append(playlistEditBtns);
        playlistRow.append(playlistNameArea).append(playlistEditArea);
        liPlaylist.append(playlistRow);
        this.playlistsDom.append(liPlaylist);
    }

    loadPlaylists()
    {
        this.playlistsDom.find('li').each(
            function () {
                this.remove();
            }
        );
        let playlistNames = this.playlist.list();
        for (let index in playlistNames) {
            this.appendPlaylistsDom(playlistNames[index]);
        }
    }

    appendPlaylists()
    {
        let result = this.playlist.append($('#new-playlist-name').val());
        if (!result) {
            return false;
        }
        this.appendPlaylistsDom(result);
        return true;
    }

    rename(row)
    {
        let playlistNameInput = row.find('div.playlist-name-area > input.playlist-name-input');
        let oldPlaylistName = playlistNameInput.attr('data-oldplaylistname');
        let newPlaylistName = playlistNameInput.val();
        let result = this.playlist.rename(oldPlaylistName, newPlaylistName);
        if (!result) {
            return false;
        }
        row.find('div.playlist-name-area > a.playlist-name-label').text(result).attr('data-playlistnamevalue', result);
        return true;
    }

    remove(row)
    {
        let playlistName = row.find('.playlist-name-area').text();
        if (this.playlist.remove(playlistName)) {
            row.remove();
        }
    }

    rebuild()
    {
        let playlistNames = new Array();
        this.playlistsDom.find('li').each(
            function () {
                let playlistName = $(this).find('.playlist-name-label').text();
                playlistNames.push(playlistName);
            }
        );
        this.playlist.rebuild(playlistNames);
        this.loadPlaylists();
        this.editPlaylistOn();
    }

    sortbleOnOff()
    {
        let editings = this.playlistsDom.find(".editing");
        if (editings.length > 0) {
            this.sortable.option("disabled", true);
            return;
        }
        this.sortable.option("disabled", false);
    }

}
