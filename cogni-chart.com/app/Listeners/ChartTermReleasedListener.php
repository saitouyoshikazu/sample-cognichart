<?php

namespace App\Listeners;

use App\Events\ChartTermReleased;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Chart\ChartApplicationInterface;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Domain\SEO\SEOServiceInterface;
use App\Application\Sns\SnsApplicationInterface;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;
use App\Application\DXO\SnsDXO;

class ChartTermReleasedListener
{

    private $chartApplication;
    private $chartTermApplication;
    private $snsApplication;
    private $seoService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        ChartApplicationInterface $chartApplication,
        ChartTermApplicationInterface $chartTermApplication,
        SnsApplicationInterface $snsApplication,
        SEOServiceInterface $seoService
    ) {
        $this->chartApplication = $chartApplication;
        $this->chartTermApplication = $chartTermApplication;
        $this->snsApplication = $snsApplication;
        $this->seoService = $seoService;
    }

    /**
     * Handle the event.
     *
     * @param  ChartTermReleased  $event
     * @return void
     */
    public function handle(ChartTermReleased $event)
    {
        $entityIdValue = $event->entityIdValue();
        $chartIdValue = $event->chartIdValue();
        $endDateValue = $event->endDateValue();
        $countryIdValue = $event->countryIdValue();
        $chartNameValue = $event->chartNameValue();
        $publishReleasedMessageValue = $event->publishReleasedMessageValue();
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $this->chartTermApplication->refreshCachedAggregation($chartTermDXO);

        if (empty($chartIdValue) || empty($countryIdValue) || empty($chartNameValue)) {
            return;
        }
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($chartIdValue, $countryIdValue, $chartNameValue);
        $this->chartApplication->refreshCachedAggregation($chartDXO);

        if ($publishReleasedMessageValue) {
            exec('nohup php /var/www/cogni-chart.com/artisan SNS:tweet "'.$countryIdValue.'" "'.$chartNameValue.'" "'.$endDateValue.'" > /dev/null &');
/*
            $snsDXO = new SnsDXO();
            $snsDXO->publishReleasedMessage($countryIdValue, $chartNameValue, $endDateValue);
            $this->snsApplication->publishReleasedMessage($snsDXO);
 */
        }

        $this->seoService->sitemapxml();
    }

}
