@include('www.chart.charttitle')
<section class="col-sm-10 offset-sm-1 col-12 wwwchartsource">
    <div class="text-center">
        @include('www.chart.chartsource')
    </div>
</section>
@include('www.chart.appendall')
<div id="append-to-playlist-modal" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="append-to-playlist-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <input type="hidden" name="selectedartistidvalue" id="selectedartistidvalue" value="">
        <input type="hidden" name="selectedmusicidvalue" id="selectedmusicidvalue" value="">
        <input type="hidden" name="selectedartistnamevalue" id="selectedartistnamevalue" value="">
        <input type="hidden" name="selectedmusictitlevalue" id="selectedmusictitlevalue" value="">
        <input type="hidden" name="selectedpromotionvideourlvalue" id="selectedpromotionvideourlvalue" value="">
        <input type="hidden" name="selecteditunesbaseurlvalue" id="selecteditunesbaseurlvalue" value="">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title" id="append-to-playlist-modal-label">
                    Append to playlist
                </strong>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-body-sizing">
                <ul id="playlists-appended" class="list-group">
                </ul>
                <div class="card">
                    <div class="card-body text-center">
                        <input type="text" name="instant_playlist_name" id="instant-playlist-name" value="" class="form-control">
                        <button type="button" class="btn btn-sm btn-primary" id="create-instant-playlist">Create new playlist</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="col-sm-10 offset-sm-1 col-12 wwwcontent" id="wwwcontentpart">
@include('www.chartterm.chartterm')
</section>
