<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\AbstractChartTermCreated' => [
            'App\Listeners\AbstractChartTermCreatedListener'
        ],
        'App\Events\ArtistMusicResolved' => [
            'App\Listeners\ArtistMusicResolvedListener'
        ],
        'App\Events\ArtistModified' => [
            'App\Listeners\ArtistModifiedListener'
        ],
        'App\Events\ArtistRollbacked' => [
            'App\Listeners\ArtistRollbackedListener'
        ],
        'App\Events\ArtistDeleted' => [
            'App\Listeners\ArtistDeletedListener'
        ],
        'App\Events\ChartReleased' => [
            'App\Listeners\ChartReleasedListner'
        ],
        'App\Events\ChartModified' => [
            'App\Listeners\ChartModifiedListner'
        ],
        'App\Events\ChartRollbacked' => [
            'App\Listeners\ChartRollbackedListner'
        ],
        'App\Events\ChartTermReleased' => [
            'App\Listeners\ChartTermReleasedListener'
        ],
        'App\Events\ChartTermRollbacked' => [
            'App\Listeners\ChartTermRollbackedListener'
        ],
        'App\Events\ChartRankingItemCreated' => [
            'App\Listeners\ChartRankingItemCreatedListener'
        ],
        'App\Events\ChartRankingItemModified' => [
            'App\Listeners\ChartRankingItemModifiedListener'
        ],
        'App\Events\MusicRegistered' => [
            'App\Listeners\MusicRegisteredListener'
        ],
        'App\Events\MusicModified' => [
            'App\Listeners\MusicModifiedListener'
        ],
        'App\Events\MusicRollbacked' => [
            'App\Listeners\MusicRollbackedListener'
        ],
        'App\Events\MusicDeleted' => [
            'App\Listeners\MusicDeletedListener'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
