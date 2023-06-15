<?php

namespace App\Listeners;

use App\Events\ChartReleased;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Chart\ChartApplicationInterface;
use App\Domain\SEO\SEOServiceInterface;
use App\Application\DXO\ChartDXO;

class ChartReleasedListner
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
     * @param  ChartReleased  $event
     * @return void
     */
    public function handle(ChartReleased $event)
    {
        $this->chartApplication->refreshCachedChartList();

        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($event->entityIdValue(), $event->countryIdValue(), $event->chartNameValue());
        $this->chartApplication->refreshCachedAggregation($chartDXO);

        $this->seoService->sitemapxml();
    }

}
