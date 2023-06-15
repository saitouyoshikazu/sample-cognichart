@php
    use App\Domain\ValueObjects\Phase;
    $artistRepository = app('App\Domain\Artist\ArtistRepositoryInterface');
    $musicRepository = app('App\Domain\Music\MusicRepositoryInterface');
@endphp
@if (!empty($chartRankingItemEntity))
@php
    $artistId = $chartRankingItemEntity->artistId();
    $musicId = $chartRankingItemEntity->musicId();
    $artist_phase = Phase::provisioned;
    $artistEntity = null;
    $music_phase = Phase::provisioned;
    $musicEntity = null;
    if (!empty($artistId)) {
        $artistEntity = $artistRepository->findProvision($artistId);
        if (empty($artistEntity)) {
            $artistEntity = $artistRepository->findRelease($artistId);
            $artist_phase = Phase::released;
        }
    }
    if (!empty($musicId)) {
        $musicEntity = $musicRepository->findProvision($musicId);
        if (empty($musicEntity)) {
            $musicEntity = $musicRepository->findRelease($musicId);
            $music_phase = Phase::released;
        }
    }
@endphp
<div class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#itunesSearchResultModal"
data-chartartistvalue="{{ $chartRankingItemEntity->chartArtist()->value() }}"
data-chartmusicvalue="{{ $chartRankingItemEntity->chartMusic()->value() }}"
>
                <i class="fas fa-search"></i>&nbsp;iTunes Search
            </button>
        </div>
    </nav>
</div>

<div class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                {{ $chartRankingItemEntity->chartArtist()->value() }}
            </div>
        </div>
    </nav>
    <article class="card-body{{ ($artist_phase === Phase::provisioned) ? ' bg-danger' : '' }}">
        @include(
            'admin.artist.artist',
            [
                'chartRankingItemEntity' => $chartRankingItemEntity,
                'artist_phase' => $artist_phase,
                'artistEntity' => $artistEntity,
            ]
        )
    </article>
</div>

<div class="card w-100{{ ($music_phase === Phase::provisioned) ? ' bg-danger' : '' }}">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <div class="navbar-brand">
                {{ $chartRankingItemEntity->chartMusic()->value() }}
            </div>
        </div>
    </nav>
    <article class="card-body">
        @include(
            'admin.music.music',
            [
                'chartRankingItemEntity' => $chartRankingItemEntity,
                'artistEntity' => $artistEntity,
                'music_phase' => $music_phase,
                'musicEntity' => $musicEntity,
            ]
        )
    </article>
</div>
@endif
