<?php

namespace App\Listeners;

use App\Events\ArtistDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\ChartRankingItemDXO;
use App\Application\DXO\MusicDXO;

class ArtistDeletedListener
{

    private $chartRankingItemApplication;
    private $musicApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        MusicApplicationInterface $musicApplication
    ) {
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->musicApplication = $musicApplication;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ArtistDeleted $event)
    {
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachArtist($event->entityIdValue());
        $this->chartRankingItemApplication->detachArtist($chartRankingItemDXO);

        $musicDXO = new MusicDXO();
        $musicDXO->deleteWithITunesArtistId($event->oldITunesArtistIdValue());
        $this->musicApplication->deleteWithITunesArtistId($musicDXO);
    }

}
