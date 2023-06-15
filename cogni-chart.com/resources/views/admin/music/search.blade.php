@extends('templates.admin', ['adminMenu' => 'Music'])

@section('content')
@php
    use App\Domain\ValueObjects\Phase;
    use App\Domain\Artist\ArtistBusinessId;
    $artistRepository = app('App\Domain\Artist\ArtistRepositoryInterface');
@endphp
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            @if ($music_phase === Phase::released)
            <form action="{{ route('music/search', ['music_phase' => Phase::provisioned]) }}" method="get" class="form-inline">
                <button type="submit" class="btn btn btn-secondary">
                    Search Provisioned
                </button>
            </form>
            <div class="navbar-brand">
                <strong>
                    Search Released
                </strong>
            </div>
            @else
            <div class="navbar-brand">
                <strong>
                    Search Provisioned
                </strong>
            </div>
            <form action="{{ route('music/search', ['music_phase' => Phase::released]) }}" method="get" class="form-inline">
                <button type="submit" class="btn btn btn-secondary">
                    Search Released
                </button>
            </form>
            @endif
            <form action="{{ route('music/promotion_video_broken_links') }}" method="get" class="form-inline">
                <button type="submit" class="btn btn btn-secondary">
                    Promotion Video Broken Links
                </button>
            </form>
        </div>
        <div class="navbar-nav w-100 my-2 justify-content-around">
            <form action="{{ route('music/search', ['music_phase' => $music_phase]) }}" class="form-inline w-100" method="get">
                <div class="form-row w-100">
                    <div class="col-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    iTunes Artist ID
                                </div>
                            </div>
                            <input type="text" name="search_itunes_artist_id" id="search-itunes-artist-id" value="{{ !empty(old('search_itunes_artist_id')) ? old('search_itunes_artist_id') : $search_itunes_artist_id }}" class="form-control" placeholder="iTunes Artist ID">
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    Music Title
                                </div>
                            </div>
                            <input type="text" name="search_music_title" id="search-music-title" value="{{ !empty(old('search_music_title')) ? old('search_music_title') : $search_music_title }}" class="form-control" placeholder="Music Title">
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="input-group">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-search"></i>&nbsp;Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </nav>
    <article class="card-body">
        @if (!empty($musicEntities))
        @foreach ($musicEntities AS $musicEntity)
        @php
            $artistBusinessId = new ArtistBusinessId($musicEntity->iTunesArtistId());
            $artistEntity = $artistRepository->getProvision($artistBusinessId);
            if (empty($artistEntity)) {
                $artistEntity = $artistRepository->getRelease($artistBusinessId);
            }
        @endphp
        @include(
            'admin.music.music',
            [
                'artistEntity' => $artistEntity,
                'music_phase' => $music_phase,
                'musicEntity' => $musicEntity,
            ]
        )
        @endforeach
        @endif
        @if (!empty($musicPaginator))
        {{ $musicPaginator->appends(['search_music_title' => $search_music_title])->links() }}
        @endif
    </article>
</section>
@endsection
