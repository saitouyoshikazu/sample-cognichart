<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApplicationProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('App\Application\AdminUser\AdminUserApplicationInterface', 'App\Application\AdminUser\AdminUserApplication');
        $this->app->singleton('App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface', 'App\Application\AbstractArtistMusic\AbstractArtistMusicApplication');
        $this->app->singleton('App\Application\AbstractChartTerm\AbstractChartTermApplicationInterface', 'App\Application\AbstractChartTerm\AbstractChartTermApplication');
        $this->app->singleton('App\Application\Artist\ArtistApplicationInterface', 'App\Application\Artist\ArtistApplication');
        $this->app->singleton('App\Application\Chart\ChartApplicationInterface', 'App\Application\Chart\ChartApplication');
        $this->app->singleton('App\Application\ChartTerm\ChartTermApplicationInterface', 'App\Application\ChartTerm\ChartTermApplication');
        $this->app->singleton('App\Application\ChartRankingItem\ChartRankingItemApplicationInterface', 'App\Application\ChartRankingItem\ChartRankingItemApplication');
        $this->app->singleton('App\Application\Music\MusicApplicationInterface', 'App\Application\Music\MusicApplication');
        $this->app->singleton('App\Application\Sns\SnsApplicationInterface', 'App\Application\Sns\SnsApplication');
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
