<?php

namespace App\Domain\AbstractArtistMusic;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;

interface AbstractArtistMusicServiceInterface
{

    /**
     * Constructor.
     * @param   AbstractArtistMusicRepositoryInterface  $abstractArtistMusicRepository  AbstractArtistMusicRepository.
     */
    public function __construct(AbstractArtistMusicRepositoryInterface $abstractArtistMusicRepository);

    /**
     * Prepare api response for resolving Artist and Music.
     * @param   EntityId        $chartRankingItemId     The id of ChartRankingItem will be resolved.
     * @param   ChartArtist     $chartArtist            ChartArtist.
     * @param   ChartMusic      $chartMusic             ChartMusic.
     * @param   ApiName         $apiName                Name of api.
     * @param   string          $scheme                 Scheme of api.
     * @param   string          $host                   Host of api.
     * @param   string          $uri                    Uri of api.
     */
    public function prepare(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName,
        string $scheme,
        string $host,
        string $uri
    );

    /**
     * Resolve Artist and Music.
     * @param   EntityId        $chartRankingItemId     The id of ChartRankingItem will be resolved.
     * @param   ChartArtist     $chartArtist            ChartArtist.
     * @param   ChartMusic      $chartMusic             ChartMusic.
     * @param   ApiName         $apiName                Name of api.
     * @return  ArtistMusicResolved
     */
    public function resolve(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName
    );

}
