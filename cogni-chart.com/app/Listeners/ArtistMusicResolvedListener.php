<?php

namespace App\Listeners;

use App\Events\ArtistMusicResolved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\Application\Artist\ArtistApplicationInterface;
use App\Application\Music\MusicApplicationInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Application\DXO\ArtistDXO;
use App\Application\DXO\MusicDXO;
use App\Application\DXO\ChartRankingItemDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\Artist\ArtistException;
use App\Domain\Music\MusicException;
use App\Domain\ChartRankingItem\ChartRankingItemException;

class ArtistMusicResolvedListener
{

    private $artistApplication;
    private $musicApplication;
    private $chartRankingItemApplication;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        ArtistApplicationInterface $artistApplication,
        MusicApplicationInterface $musicApplication,
        ChartRankingItemApplicationInterface $chartRankingItemApplication
    ) {
        $this->artistApplication = $artistApplication;
        $this->musicApplication = $musicApplication;
        $this->chartRankingItemApplication = $chartRankingItemApplication;
    }

    /**
     * Handle the event.
     *
     * @param  ArtistMusicResolved  $event
     * @return void
     */
    public function handle(ArtistMusicResolved $event)
    {
        $artistDXO = new ArtistDXO();
        $artistDXO->register($event->resolvedArtistIdValue(), $event->resolvedArtistValue());
        try {
            $result = $this->artistApplication->register($artistDXO);
            if ($result === false) {
                Log::error("Failed to register ArtistEntity.");
                return false;
            }
        } catch (ArtistException $e) {
            if ($e->getMessage() !== "Couldn't register to provision ArtistEntity because released Artist is already existing."     &&
                $e->getMessage() !== "Couldn't register to provision ArtistEntity because provisioned Artist is already existing."  ) {
                throw $e;
            }
        }

        $musicDXO = new MusicDXO();
        $musicDXO->register(
            $event->resolvedArtistIdValue(),
            $event->resolvedMusicValue(),
            $event->resolvedITunesBaseUrlValue()
        );
        try {
            $result = $this->musicApplication->register($musicDXO);
            if ($result === false) {
                Log::error("Failed to register MusicEntity.");
                return false;
            }
        } catch (MusicException $e) {
            if ($e->getMessage() !== "Couldn't register to provision MusicEntity because released Music is already existing."       &&
                $e->getMessage() !== "Couldn't register to provision MusicEntity because provisioned Music is already existing."    ) {
                throw $e;
            }
        }

        $artistDXO = new ArtistDXO();
        $artistDXO->get(Phase::provisioned, $event->resolvedArtistIdValue());
        $artistEntity = $this->artistApplication->get($artistDXO);
        if (empty($artistEntity)) {
            $artistDXO = new ArtistDXO();
            $artistDXO->get(Phase::released, $event->resolvedArtistIdValue());
            $artistEntity = $this->artistApplication->get($artistDXO);
        }
        if (empty($artistEntity)) {
            Log::error("Couldn't find ArtistEntity.");
            return false;
        }

        $musicDXO = new MusicDXO();
        $musicDXO->get(Phase::provisioned, $event->resolvedArtistIdValue(), $event->resolvedMusicValue());
        $musicEntity = $this->musicApplication->get($musicDXO);
        if (empty($musicEntity)) {
            $musicDXO = new MusicDXO();
            $musicDXO->get(Phase::released, $event->resolvedArtistIdValue(), $event->resolvedMusicValue());
            $musicEntity = $this->musicApplication->get($musicDXO);
        }
        if (empty($musicEntity)) {
            Log::error("Couldn't find MusicEntity.");
            return false;
        }

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->find($event->chartRankingItemIdValue());
        $chartRankingItemEntity = $this->chartRankingItemApplication->find($chartRankingItemDXO);
        if (empty($chartRankingItemEntity)) {
            Log::error("Couldn't find ChartRankingItemEntity.");
            return false;
        }

        $chartRankingItemDXO = new ChartRankingItemDXO();
        $chartRankingItemDXO->modify(
            $chartRankingItemEntity->id()->value(),
            $chartRankingItemEntity->chartArtist()->value(),
            $chartRankingItemEntity->chartMusic()->value(),
            $artistEntity->id()->value(),
            $musicEntity->id()->value()
        );
        $result = $this->chartRankingItemApplication->modify($chartRankingItemDXO);
        if ($result === false) {
            Log::error("Failed to modify ChartRankinItem.");
            return false;
        }
        return true;
    }

}
