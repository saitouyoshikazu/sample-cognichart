<section class="w-100 pt-1 pb-1 d-flex justify-content-center">
    <button type="button" class="btn btn-lime appendall-btn" data-toggle="modal" data-target="#append-all-songs-modal">
        <i class="fas fa-list"></i><i class="fas fa-reply-all"></i>&nbsp;append all songs to playlist
    </button>
</section>

<div id="append-all-songs-modal" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="append-all-songs-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title" id="append-all-songs-modal-label">
                    Append all songs of this chart to playlist
                </strong>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-sizing">
                <ul id="append-all-songs-playlists" class="list-group w-100">
                </ul>
                <div class="card w-100" id="append-all-songs-instant-playlist-row">
                    <div class="card-body text-center">
                        <input type="text" name="append_all_songs_instant_playlist_name" id="append-all-songs-instant-playlist-name" value="" class="form-control">
                        <button type="button" class="btn btn-sm btn-primary" id="create-append-all-songs-instant-playlist">
                            Create new playlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
