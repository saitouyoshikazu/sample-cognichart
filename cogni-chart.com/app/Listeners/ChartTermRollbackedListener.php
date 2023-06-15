<?php

namespace App\Listeners;

use App\Events\ChartTermRollbacked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Domain\SEO\SEOServiceInterface;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;

class ChartTermRollbackedListener
{

    private $chartApplication;
    private $chartTermApplication;
    private $seoService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        ChartApplicationInterface $chartApplication,
        ChartTermApplicationInterface $chartTermApplication,
        SEOServiceInterface $seoService
    ) {
        $this->chartApplication = $chartApplication;
        $this->chartTermApplication = $chartTermApplication;
        $this->seoService = $seoService;
    }

    /**
     * Handle the event.
     *
     * @param  ChartTermRollbacked  $event
     * @return void
     */
    public function handle(ChartTermRollbacked $event)
    {
        $entityIdValue = $event->entityIdValue();
        $chartIdValue = $event->chartIdValue();
        $endDateValue = $event->endDateValue();
        $countryIdValue = $event->countryIdValue();
        $chartNameValue = $event->chartNameValue();
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $this->chartTermApplication->refreshCachedAggregation($chartTermDXO);

        if (empty($chartIdValue) || empty($countryIdValue) || empty($chartNameValue)) {
            return;
        }
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($chartIdValue, $countryIdValue, $chartNameValue);
        $this->chartApplication->refreshCachedAggregation($chartDXO);

        $this->seoService->sitemapxml();
    }

}
