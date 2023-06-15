<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class InfrastructureProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'testing') {
            $this->app->singleton('App\Infrastructure\RedisDAO\RedisDAOInterface', 'Tests\Infrastructure\RedisDAO\TestRedisDAO');
        } else {
            $this->app->singleton('App\Infrastructure\RedisDAO\RedisDAOInterface', 'App\Infrastructure\RedisDAO\RedisDAO');
        }
        $this->app->singleton('App\Infrastructure\Remote\RemoteInterface', 'App\Infrastructure\Remote\Guzzle\Remote');
        $this->app->singleton('App\Infrastructure\Storage\AbstractChartTermStorageInterface', 'App\Infrastructure\Storage\AbstractChartTermStorage');
        $this->app->singleton('App\Infrastructure\Sns\TwitterInterface', 'App\Infrastructure\Sns\Twitter');
        $this->app->singleton('App\Infrastructure\Sns\FacebookInterface', 'App\Infrastructure\Sns\Facebook');
        $this->app->singleton('App\Infrastructure\Sns\GMailerInterface', 'App\Infrastructure\Sns\GMailer');
        $this->app->singleton('App\Infrastructure\SEO\SiteMapXmlInterface', 'App\Infrastructure\SEO\SiteMapXml');
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
