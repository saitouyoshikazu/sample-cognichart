<?php

namespace App\Listeners;

use App\Events\MusicDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Music\MusicApplicationInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\DXO\MusicDXO;
use App\Application\DXO\ChartRankingItemDXO;

class MusicDeletedListener
{

    private $musicApplication;
    private $chartRankingItemApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MusicApplicationInterface $musicApplication,
        ChartRankingItemApplicationInterface $chartRankingItemApplication
    ) {
        $this->musicApplication = $musicApplication;
        $this->chartRankingItemApplication = $chartRankingItemApplication;
    }

    /**
     * Handle the event.
     *
     * @param  MusicModified  $event
     * @return void
     */
    public function handle(MusicDeleted $event)
    {
        $musicDXO = new MusicDXO();
        $musicDXO->deletePromotionVideoBrokenLink($event->entityIdValue());
        $this->musicApplication->deletePromotionVideoBrokenLink($musicDXO);

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->detachMusic($event->entityIdValue());
        $this->chartRankingItemApplication->detachMusic($chartRankingItemDXO);
    }

}
