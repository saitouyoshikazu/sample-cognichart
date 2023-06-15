<?php

namespace App\Listeners;

use App\Events\MusicModified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\MusicDXO;

class MusicModifiedListener
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
     * @param  MusicModified  $event
     * @return void
     */
    public function handle(MusicModified $event)
    {
        $musicDXO = new MusicDXO();
        $musicDXO->refreshCachedEntity($event->entityIdValue());
        $this->musicApplication->refreshCachedEntity($musicDXO);

        $musicDXO = new MusicDXO();
        $musicDXO->deletePromotionVideoBrokenLink($event->entityIdValue());
        $this->musicApplication->deletePromotionVideoBrokenLink($musicDXO);
    }

}
