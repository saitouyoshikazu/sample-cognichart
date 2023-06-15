<?php

namespace App\Listeners;

use App\Events\ArtistModified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\ArtistDXO;
use App\Application\DXO\MusicDXO;

class ArtistModifiedListener
{

    private $artistApplication;
    private $musicApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        ArtistApplicationInterface $artistApplication,
        MusicApplicationInterface $musicApplication
    ) {
        $this->artistApplication = $artistApplication;
        $this->musicApplication = $musicApplication;
    }

    /**
     * Handle the event.
     *
     * @param  ArtistModified  $event
     * @return void
     */
    public function handle(ArtistModified $event)
    {
        $artistDXO = new ArtistDXO();
        $artistDXO->refreshCachedEntity($event->entityIdValue());
        $this->artistApplication->refreshCachedEntity($artistDXO);

        $musicDXO = new MusicDXO();
        $musicDXO->replaceITunesArtistId($event->oldITunesArtistIdValue(), $event->iTunesArtistIdValue());
        $this->musicApplication->replaceITunesArtistId($musicDXO);
    }

}
