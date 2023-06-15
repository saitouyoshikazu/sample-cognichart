<?php

namespace App\Application\AbstractArtistMusic;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicServiceInterface;
use Config;
use Event;
use App\Application\DXO\AbstractArtistMusicDXO;
use App\Domain\ValueObjects\ApiName;

class AbstractArtistMusicApplication implements AbstractArtistMusicApplicationInterface
{

    private $abstractArtistMusicService;

    public function __construct(AbstractArtistMusicServiceInterface $abstractArtistMusicService)
    {
        $this->abstractArtistMusicService = $abstractArtistMusicService;
    }

    public function prepare(AbstractArtistMusicDXO $abstractArtistMusicDXO)
    {
        $entityId = $abstractArtistMusicDXO->getEntityId();
        $chartArtist = $abstractArtistMusicDXO->getChartArtist();
        $chartMusic = $abstractArtistMusicDXO->getChartMusic();
        if (empty($entityId) || empty($chartArtist) || empty($chartMusic)) {
            return false;
        }
        $apies = Config::get('app.artist_music_resolve_api');
        if (empty($apies)) {
            return false;
        }
        foreach ($apies AS $api => $settings) {
            $apiName = new ApiName($api);
            $result = $this->abstractArtistMusicService->prepare(
                $entityId,
                $chartArtist,
                $chartMusic,
                $apiName,
                $settings['scheme'],
                $settings['host'],
                $settings['uri']
            );
            if ($result === false) {
                continue;
            }
        }
        return true;
    }

    public function resolve(AbstractArtistMusicDXO $abstractArtistMusicDXO)
    {
        $entityId = $abstractArtistMusicDXO->getEntityId();
        $chartArtist = $abstractArtistMusicDXO->getChartArtist();
        $chartMusic = $abstractArtistMusicDXO->getChartMusic();
        if (empty($entityId) || empty($chartArtist) || empty($chartMusic)) {
            return false;
        }
        $apies = Config::get('app.artist_music_resolve_api');
        if (empty($apies)) {
            return false;
        }
        foreach ($apies AS $api => $settings) {
            $apiName = new ApiName($api);
            $artistMusicResolved = $this->abstractArtistMusicService->resolve(
                $entityId,
                $chartArtist,
                $chartMusic,
                $apiName
            );
            if (empty($artistMusicResolved)) {
                continue;
            }
            Event::dispatch($artistMusicResolved);
        }
        return true;
    }

}
