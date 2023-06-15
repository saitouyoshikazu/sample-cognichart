<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomainProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('App\Domain\AdminUser\AdminUserRepositoryInterface', 'App\Infrastructure\Repositories\AdminUserRepository');
        $this->app->singleton('App\Domain\AdminUser\AdminUserFactoryInterface', 'App\Domain\AdminUser\AdminUserFactory');

        $this->app->singleton('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface', 'App\Infrastructure\Repositories\AbstractChartTermRepository');
        $this->app->singleton('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface', 'App\Domain\AbstractChartTerm\AbstractChartTermService');

        $this->app->singleton('App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface', 'App\Infrastructure\Repositories\AbstractArtistMusicRepository');
        $this->app->singleton('App\Domain\AbstractArtistMusic\AbstractArtistMusicServiceInterface', 'App\Domain\AbstractArtistMusic\AbstractArtistMusicService');

        $this->app->singleton('App\Domain\Artist\ArtistRepositoryInterface', 'App\Infrastructure\Repositories\ArtistRepository');
        $this->app->singleton('App\Domain\Artist\ArtistFactoryInterface', 'App\Domain\Artist\ArtistFactory');

        $this->app->singleton('App\Domain\Country\CountryRepositoryInterface',  'App\Infrastructure\Repositories\CountryRepository');
        $this->app->singleton('App\Domain\Country\CountryFactoryInterface', 'App\Domain\Country\CountryFactory');

        $this->app->singleton('App\Domain\Chart\ChartListRepositoryInterface', 'App\Infrastructure\Repositories\ChartListRepository');
        $this->app->singleton('App\Domain\Chart\ChartRepositoryInterface', 'App\Infrastructure\Repositories\ChartRepository');
        $this->app->singleton('App\Domain\Chart\ChartFactoryInterface', 'App\Domain\Chart\ChartFactory');

        $this->app->singleton('App\Domain\ChartTerm\ChartTermListRepositoryInterface', 'App\Infrastructure\Repositories\ChartTermListRepository');
        $this->app->singleton('App\Domain\ChartTerm\ChartTermRepositoryInterface', 'App\Infrastructure\Repositories\ChartTermRepository');
        $this->app->singleton('App\Domain\ChartTerm\ChartTermFactoryInterface', 'App\Domain\ChartTerm\ChartTermFactory');

        $this->app->singleton('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface', 'App\Infrastructure\Repositories\ChartRankingItemRepository');
        $this->app->singleton('App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface', 'App\Domain\ChartRankingItem\ChartRankingItemFactory');

        $this->app->singleton('App\Domain\Music\MusicRepositoryInterface', 'App\Infrastructure\Repositories\MusicRepository');
        $this->app->singleton('App\Domain\Music\MusicFactoryInterface', 'App\Domain\Music\MusicFactory');
        $this->app->singleton('App\Domain\Music\MusicServiceInterface', 'App\Domain\Music\MusicService');

        $this->app->singleton('App\Domain\SEO\SEOServiceInterface', 'App\Domain\SEO\SEOService');
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
