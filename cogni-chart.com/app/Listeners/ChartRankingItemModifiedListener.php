<?php

namespace App\Listeners;

use App\Events\ChartRankingItemModified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\DXO\ChartRankingItemDXO;

class ChartRankingItemModifiedListener
{

    private $chartRankingItemApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ChartRankingItemApplicationInterface $chartRankingItemApplication)
    {
        $this->chartRankingItemApplication = $chartRankingItemApplication;
    }

    /**
     * Handle the event.
     *
     * @param  ChartRankingItemModified  $event
     * @return void
     */
    public function handle(ChartRankingItemModified $event)
    {
        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->refreshCachedEntity($event->entityIdValue());
        $this->chartRankingItemApplication->refreshCachedEntity($chartRankingItemDXO);
    }

}
