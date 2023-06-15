<div class="row chartrankingitem-row">
    <div class="w-100 d-flex justify-content-start">
        <div class="ranking-box-left position-relative">
            <p class="transform-center">
                {{ $chartRanking->ranking() }}
            </p>
        </div>
        <div class="chartrankingitem-outer-box">
            <div class="w-100 d-flex justify-content-start">
                <div class="thumbnail-box">
                    @if (!empty($musicEntity) && !empty($musicEntity->promotionVideoUrl()) && !empty($musicEntity->thumbnailUrl()))
                    <button type="button" class="playpvbutton" data-promotionvideourlvalue="{{ $musicEntity->promotionVideoUrl()->value() }}">
                        <i>
                            <img src="{{ asset('png/thumbnail-empty.png') }}" class="pv-thumbnail" data-original="{{ $musicEntity->thumbnailUrl()->value() }}" alt="{{ $chartRankingItemEntity->chartMusic()->value() . ' ' . $chartRankingItemEntity->chartArtist()->value() }}">
                            <img src="{{ asset('png/play-circle.png') }}" class="play-circle" alt="play">
                        </i>
                    </button>
                    @else
                    <img src="{{ asset('png/not-found.png') }}" class="not-found" alt="{{ $chartRankingItemEntity->chartMusic()->value() . ' ' . $chartRankingItemEntity->chartArtist()->value() }}">
                    @endif
                </div>
                <div class="chartrankingitem-box">
                    <div class="ranking-box-upper position-relative">
                        <p class="transform-center">
                            {{ $chartRanking->ranking() }}
                        </p>
                    </div>
                    <div class="chart-music-box pl-1">
                        @if (!empty($musicEntity) && !empty($artistEntity) && !empty($musicEntity->promotionVideoUrl()))
                        <a class="pv-data-link" href="javascript:void(0);" data-toggle="modal" data-target="#append-to-playlist-modal"
data-artistidvalue="{{ $artistEntity->id()->value() }}"
data-musicidvalue="{{ $musicEntity->id()->value() }}"
data-artistnamevalue="{{ $artistEntity->artistName()->value() }}"
data-musictitlevalue="{{ $musicEntity->musicTitle()->value() }}"
data-promotionvideourlvalue="{{ $musicEntity->promotionVideoUrl()->value() }}"
data-itunesbaseurlvalue="{{ !empty($musicEntity->iTunesBaseUrl()) ? $musicEntity->iTunesBaseUrl()->value() : '' }}">
                            {{ $chartRankingItemEntity->chartMusic()->value() }}
                        </a>
                        @else
                        <span>
                            {{ $chartRankingItemEntity->chartMusic()->value() }}
                        </span>
                        @endif
                    </div>
                    <div class="chart-artist-box pl-1">
                        {{ $chartRankingItemEntity->chartArtist()->value() }}
                    </div>
                </div>
            </div>
            <div class="w-100">
@if (!empty($musicEntity) && !empty($musicEntity->iTunesBaseUrl()))
                <a href="{{ $musicEntity->iTunesBaseUrl()->value() }}&mt=1&app=music&at={{ config('app.itunes_affiliate_token') }}" class="af-button" target="_blank" rel="nofollow sponsored">
                    <img src="https://linkmaker.itunes.apple.com/en-us/badge-lrg.svg?kind=song&bubble=apple_music" alt="Listen on Apple Music"></img>
                </a>
                <a href="{{ $musicEntity->iTunesBaseUrl()->value() }}&mt=1&app=itunes&at={{ config('app.itunes_affiliate_token') }}" class="af-button" target="_blank" rel="nofollow sponsored">
                    <img src="https://linkmaker.itunes.apple.com/en-us/badge-lrg.svg?kind=song&bubble=itunes_music" alt="Buy on iTunes Store"></img>
                </a>
@endif
            </div>
        </div>
    </div>
</div>
