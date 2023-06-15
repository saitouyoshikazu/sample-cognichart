<?php

namespace App\Listeners;

use App\Events\MusicRollbacked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\MusicDXO;

class MusicRollbackedListener
{

    private $musicApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(MusicApplicationInterface $musicApplication)
    {
        $this->musicApplication = $musicApplication;
    }

    /**
     * Handle the event.
     *
     * @param  MusicRollbacked  $event
     * @return void
     */
    public function handle(MusicRollbacked $event)
    {
        $musicDXO = new MusicDXO();
        $musicDXO->refreshCachedEntity($event->entityIdValue());
        $this->musicApplication->refreshCachedEntity($musicDXO);
    }

}
