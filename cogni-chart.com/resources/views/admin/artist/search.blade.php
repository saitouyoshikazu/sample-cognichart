@extends('templates.admin', ['adminMenu' => 'Artist'])

@section('content')
@php
    use App\Domain\ValueObjects\Phase;
@endphp
<section class="card w-100">
    <nav class="card-heading navbar navbar-expand navbar-light bg-light flex-column">
        <div class="navbar-nav w-100 justify-content-around">
            @if ($artist_phase === Phase::provisioned)
            <div class="navbar-brand">
                Search Provisioned
            </div>
            <form action="{{ route('artist/search', ['artist_phase' => Phase::released]) }}" method="get">
                <button type="submit" class="btn btn-secondary">Search Released</button>
            </form>
            @elseif ($artist_phase === Phase::released)
            <form action="{{ route('artist/search', ['artist_phase' => Phase::provisioned]) }}" method="get">
                <button type="submit" class="btn btn-secondary">Search Provisioned</button>
            </form>
            <div class="navbar-brand">
                Search Released
            </div>
            @endif
        </div>
        <div class="navbar-nav w-100 my-2 justify-content-around">
            <form action="{{ route('artist/search', ['artist_phase' => $artist_phase]) }}" method="get" class="form-inline">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            Artist Name
                        </div>
                    </div>
                    <input type="text" name="search_artist_name" value="{{ !empty(old('search_artist_name')) ? old('search_artist_name') : $search_artist_name }}" class="form-control" placeholder="Search">
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
        @if (!empty($artistEntities))
        @foreach ($artistEntities AS $artistEntity)
        @include(
            'admin.artist.artist',
            [
                'artist_phase' => $artist_phase,
                'artistEntity' => $artistEntity,
            ]
        )
        @endforeach
        @endif
        @if (!empty($artistPaginator))
        {{ $artistPaginator->appends(['search_artist_name' => $search_artist_name])->links() }}
        @endif
    </article>
</section>
@endsection
