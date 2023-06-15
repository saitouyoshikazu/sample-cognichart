<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AbstractChartTermStrategyProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //US Billboard Hot 100
        $this->app->bind('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\RequestSender'    , 'App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\RequestSender'       );
        $this->app->bind('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer'      , 'App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer'         );
        $this->app->bind('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\AdoptionCriteria' , 'App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\AdoptionCriteria'    );


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

}
