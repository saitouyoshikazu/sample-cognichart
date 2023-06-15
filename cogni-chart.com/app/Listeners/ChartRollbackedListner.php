<?php

namespace App\Listeners;

use App\Events\ChartRollbacked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Chart\ChartApplicationInterface;
use App\Domain\SEO\SEOServiceInterface;
use App\Application\DXO\ChartDXO;

class ChartRollbackedListner
{

    private $chartApplication;
    private $seoService;

    /**
     * Create the event listener.
     *
     * @param   ChartApplicationInterface   $chartApplication   ChartApplication.
     * @return void
     */
    public function __construct(
        ChartApplicationInterface $chartApplication,
        SEOServiceInterface $seoService
    ) {
        $this->chartApplication = $chartApplication;
        $this->seoService = $seoService;
    }

    /**
     * Handle the event.
     *
     * @param  ChartRollbacked  $event
     * @return void
     */
    public function handle(ChartRollbacked $event)
    {
        $this->chartApplication->refreshCachedChartList();

        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($event->entityIdValue(), $event->countryIdValue(), $event->chartNameValue());
        $this->chartApplication->refreshCachedAggregation($chartDXO);

        $this->seoService->sitemapxml();
    }

}
