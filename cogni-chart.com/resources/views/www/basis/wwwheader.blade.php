<nav class="chartheader navbar navbar-expand navbar-light w-100 pt-1 pb-1 fixed-top flex-column">
    <div class="navbar-nav w-100 justify-content-around">
        <div class="logobox col-6 text-center">
            @include('www.basis.cognichartlogo')
        </div>
        <div class="sns-links col-6 text-center">
            @include('www.sns.snslinks')
        </div>
    </div>
    <div class="navbar-nav w-100 justify-content-center flex-direction-control">
        <div class="chartlistpart" id="chartlistpart">
            @include('www.chart.chartlist')
        </div>
        <div class="charttermlistpart" id="charttermlistpart">
            @include('www.chart.charttermlist')
        </div>
    </div>
    <div class="navbar-nav w-100">
        <div class="player-row btn-group w-100">
            <button type="button" class="btn btn-gray btn-playway" id="display-player">
                <div class="playway-icon">
                    <i class="fab fa-youtube"></i>
                </div>
                <div class="playway-text">
                    player
                </div>
            </button>
            <button type="button" class="btn btn-gray btn-playway" data-toggle="modal" data-target="#playlist-modal">
                <div class="playway-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="playway-text">
                    playlists
                </div>
            </button>
            <button type="button" class="btn btn-gray btn-playway" id="set-playlist-loop" data-onoff="off">
                <div class="playway-icon">
                    <i class="fas fa-redo"></i>
                </div>
                <div class="playway-text">
                    OFF
                </div>
            </button>
            <button type="button" class="btn btn-gray btn-playway" id="set-playlist-random" data-onoff="off">
                <div class="playway-icon">
                    <i class="fas fa-random"></i>
                </div>
                <div class="playway-text">
                    OFF
                </div>
            </button>
        </div>
    </div>
    <div class="navbar-nav w-100 player-box justify-content-center d-none" id="player-box">
        <div id="player"></div>
    </div>
</nav>

<div id="playlist-modal" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="playlist-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <strong class="modal-title" id="playlist-modal-label">Your playlists&nbsp;</strong>
                <div class="dropdown">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="">
                        <a href="javascript:void(0);" class="dropdown-item" id="create-playlist-on">Create</a>
                        <a href="javascript:void(0);" class="dropdown-item" id="edit-playlist-on">Edit</a>
                        <a href="javascript:void(0);" class="dropdown-item" id="import-playlist">import from file</a>
                        <input type="file" name="playlist_file" class="upload-field-hidden" id="import-playlist-field">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-info d-none" id="edit-playlist-off">
                    Finish editing
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-sizing">
                <ul id="playlists" class="list-group w-100">
                </ul>
                <div class="card w-100 d-none" id="create-playlist">
                    <div class="card-body text-center">
                        <input type="text" name="new_playlist_name" id="new-playlist-name" value="" class="form-control">
                        <button type="button" class="btn btn-sm btn-primary" id="create-new-playlist">
                            Create new playlist
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="cancel-new-playlist">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="playlistitems-modal" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="playlistitems-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 d-flex justify-content-around">
                    <button type="button" class="btn btn-primary btn-sm" id="play-playlist" data-playlistnamevalue="">
                        <i class="fas fa-play"></i>&nbsp;Play
                    </button>
                    <a class="btn btn-outline-primary btn-sm" id="export-playlist-button" data-playlistnamevalue="">
                        <i class="fas fa-file-import"></i>&nbsp;Export
                    </a>
                    <button type="button" class="btn btn-warning btn-sm" id="reset-promotion-videos-button" data-playlistnamevalue="" data-loading-text="<i class='fas fa-sync loading-spin'></i>&nbsp;Reset PV">
                        <i class="fas fa-sync"></i>&nbsp;Reset PV
                    </button>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-header modal-header-sizing">
                <strong class="modal-title" id="playlistitems-modal-label"></strong>
            </div>
            <div class="modal-body modal-body-sizing">
                <ul id="playlistitems" class="list-group" data-playlistnamevalue="" data-ittoken="{{ config('app.itunes_affiliate_token') }}">
                </ul>
            </div>
        </div>
    </div>
</div>
