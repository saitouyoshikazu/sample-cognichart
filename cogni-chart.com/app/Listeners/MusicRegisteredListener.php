<?php

namespace App\Listeners;

use App\Events\MusicRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\Music\MusicApplicationInterface;
use App\Application\DXO\ArtistDXO;
use App\Application\DXO\MusicDXO;
use App\Domain\ValueObjects\Phase;

class MusicRegisteredListener
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
     * @param  MusicRegistered  $event
     * @return void
     */
    public function handle(MusicRegistered $event)
    {
        $musicDXO = new MusicDXO();
        $musicDXO->find(Phase::provisioned, $event->entityIdValue());
        $musicEntity = $this->musicApplication->find($musicDXO);
        if (empty($musicEntity)) {
            return false;
        }

        $artistDXO = new ArtistDXO();
        $artistDXO->get(Phase::provisioned, $musicEntity->iTunesArtistId()->value());
        $artistEntity = $this->artistApplication->get($artistDXO);
        if (empty($artistEntity)) {
            $artistDXO = new ArtistDXO();
            $artistDXO->get(Phase::released, $musicEntity->iTunesArtistId()->value());
            $artistEntity = $this->artistApplication->get($artistDXO);
        }
        if (empty($artistEntity)) {
            return false;
        }

        $musicDXO = new MusicDXO();
        $musicDXO->assignPromotionVideo(
            $musicEntity->id()->value(),
            $artistEntity->artistName()->value(),
            $musicEntity->musicTitle()->value()
        );
        return $this->musicApplication->assignPromotionVideo($musicDXO);
    }

}
