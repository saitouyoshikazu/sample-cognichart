@extends('templates.admin', ['adminMenu' => 'Music'])

@section('content')
@php
    use App\Domain\ValueObjects\Phase;
@endphp
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            <form action="{{ route('music/search', ['music_phase' => Phase::provisioned]) }}" method="get" class="form-inline">
                <button type="submit" class="btn btn btn-secondary">
                    Search Provisioned
                </button>
            </form>
            <form action="{{ route('music/search', ['music_phase' => Phase::released]) }}" method="get" class="form-inline">
                <button type="submit" class="btn btn btn-secondary">
                    Search Released
                </button>
            </form>
            <div class="navbar-brand">
                <strong>
                    Promotion Video Broken List
                </strong>
            </div>
        </div>
        <div class="navbar-nav w-100 my-2 justify-content-around">
            <form action="{{ route('music/promotion_video_broken_links') }}" class="form-inline" method="get">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            Artist Name
                        </div>
                    </div>
                    <input type="text" name="search_artist_name" id="search-artist-name" value="{{ !empty(old('search_artist_name')) ? old('search_artist_name') : $search_artist_name }}" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>&nbsp;Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </nav>
    <article class="card-body">
        @php
            $artistRepository = app('App\Domain\Artist\ArtistRepositoryInterface');
            $musicRepository = app('App\Domain\Music\MusicRepositoryInterface');
            use App\Domain\Artist\ArtistBusinessId;
        @endphp
        @foreach ($musicEntities AS $musicEntity)
        @php
            $artistBusinessId = new ArtistBusinessId($musicEntity->iTunesArtistId());
            $artistEntity = $artistRepository->getProvision($artistBusinessId);
            if (empty($artistEntity)) {
                $artistEntity = $artistRepository->getRelease($artistBusinessId);
            }
            $phase = $musicRepository->getPhase($musicEntity->id());
        @endphp
        <div class="card w-100">
            <nav class="card-heading navbar navbar-expand navbar-light bg-light">
                <div class="navbar-nav w-100 justify-content-around">
                    <div class="navbar-brand">
                        {{ $musicEntity->musicTitle()->value() }} / {{ $artistEntity->artistName()->value() }}
                    </div>
                </div>
            </nav>
            <div class="card-body">
                <form action="{{ route('music/modify') }}" class="inline-form"  method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="music_phase" value="{{ $phase->value() }}">
                    <input type="hidden" name="music_id" value="{{ $musicEntity->id()->value() }}">
                    <input type="hidden" name="itunes_artist_id" value="{{ $musicEntity->iTunesArtistId()->value() }}">
                    <input type="hidden" name="music_title" value="{{ $musicEntity->musicTitle()->value() }}">
                    <input type="hidden" name="itunes_base_url" value="{{ !empty($musicEntity->iTunesBaseUrl()) ? $musicEntity->iTunesBaseUrl()->value() : '' }}">
                    <div class="form-row form-group">
                        <label class="col-form-labeli col-2">{{ __('promotion_video_url') }}</label>
                        <div class="col-10">
                            <input type="text" name="promotion_video_url" value="{{ !empty($musicEntity->promotionVideoUrl()) ? $musicEntity->promotionVideoUrl()->value() : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label class="col-form-labeli col-2">{{ __('thumbnail_url') }}</label>
                        <div class="col-10">
                            <input type="text" name="thumbnail_url" value="{{ !empty($musicEntity->thumbnailUrl()) ? $musicEntity->thumbnailUrl()->value() : '' }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-row justify-content-around">
                        <button type="submit" class="btn btn-primary">UPDATE</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
        {{ $musicPaginator->appends(['search_artist_name' => $search_artist_name])->links() }}
    </article>
</section>
@endsection
