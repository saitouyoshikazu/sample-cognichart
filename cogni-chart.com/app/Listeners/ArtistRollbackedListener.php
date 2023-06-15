<?php

namespace App\Listeners;

use App\Events\ArtistRollbacked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\DXO\ArtistDXO;

class ArtistRollbackedListener
{

    private $artistApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ArtistApplicationInterface $artistApplication)
    {
        $this->artistApplication = $artistApplication;
    }

    /**
     * Handle the event.
     *
     * @param  ArtistRollbacked  $event
     * @return void
     */
    public function handle(ArtistRollbacked $event)
    {
        $artistDXO = new ArtistDXO();
        $artistDXO->refreshCachedEntity($event->entityIdValue());
        $this->artistApplication->refreshCachedEntity($artistDXO);
    }
}
