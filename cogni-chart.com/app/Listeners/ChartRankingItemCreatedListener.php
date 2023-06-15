<?php

namespace App\Listeners;

use App\Events\ChartRankingItemCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface;
use App\Application\DXO\AbstractArtistMusicDXO;

class ChartRankingItemCreatedListener
{

    private $abstractArtistMusicApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AbstractArtistMusicApplicationInterface $abstractArtistMusicApplication)
    {
        $this->abstractArtistMusicApplication = $abstractArtistMusicApplication;
    }

    /**
     * Handle the event.
     *
     * @param  ChartRankingItemCreated  $event
     * @return void
     */
    public function handle(ChartRankingItemCreated $event)
    {
        $chartRankingItemIdValue = $event->chartRankingItemIdValue();
        $chartArtistValue = $event->chartArtistValue();
        $chartMusicValue = $event->chartMusicValue();

        // Artist, Musicを解決するための材料を取得する。
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->prepare($chartRankingItemIdValue, $chartArtistValue, $chartMusicValue);
        $this->abstractArtistMusicApplication->prepare($abstractArtistMusicDXO);

        // ArtistとMusicを解決
        $abstractArtistMusicDXO = new AbstractArtistMusicDXO();
        $abstractArtistMusicDXO->resolve($chartRankingItemIdValue, $chartArtistValue, $chartMusicValue);
        $this->abstractArtistMusicApplication->resolve($abstractArtistMusicDXO);
    }

}
