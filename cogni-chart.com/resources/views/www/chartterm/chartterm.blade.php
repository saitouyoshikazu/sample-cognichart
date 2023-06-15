@php
use App\Domain\ChartRankingItem\ChartRankingItemSpecification;
use App\Domain\Artist\ArtistSpecification;
use App\Domain\Music\MusicSpecification;
@endphp
@if (!empty($chartTermAggregation) && !empty($chartTermAggregation->chartRankings()))
@php
$chartRankingItemRepository = app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface');
$artistRepository = app('App\Domain\Artist\ArtistRepositoryInterface');
$musicRepository = app('App\Domain\Music\MusicRepositoryInterface');
@endphp


@foreach ($chartTermAggregation->chartRankings() AS $chartRanking)
    @php
        $chartRankingItemEntity = null;
        $artistEntity = null;
        $musicEntity = null;
    
        $chartRankingItemId = $chartRanking->chartRankingItemId();
        if (!empty($chartRankingItemId)) {
            $chartRankingItemEntity = $chartRankingItemRepository->findWithCache($chartRankingItemId, new ChartRankingItemSpecification());
            $artistId = $chartRankingItemEntity->artistId();
            $musicId = $chartRankingItemEntity->musicId();
            if (!empty($artistId)) {
                $artistEntity = $artistRepository->findWithCache($artistId, new ArtistSpecification());
            }
            if (!empty($musicId)) {
                $musicEntity = $musicRepository->findWithCache($musicId, new MusicSpecification());
            }
        }
    @endphp
    @include(
        'www.chartrankingitem.chartrankingitem',
        [
            'chartRanking' => $chartRanking,
            'chartRankingItemEntity' => $chartRankingItemEntity,
            'artistEntity' => $artistEntity,
            'musicEntity' => $musicEntity
        ]
    )
    @if ($loop->iteration == 20 || $loop->iteration == 50 || $loop->iteration == 100)
<div class="row gadBox2">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- ChartRanking内Ad -->
<ins class="adsbygoogle gad2" style="display:block;"
     data-ad-client="ca-pub-7212715746724887"
     data-ad-slot="9260128995"
     data-ad-format="auto"
     data-full-width-responsive="false"
></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
</div>
    @endif
@endforeach
@endif
