<?php

namespace App\Domain\AbstractArtistMusic;
use App\Domain\DomainRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;
use App\Domain\AbstractArtistMusic\Strategy\AbstractRequestSender;

interface AbstractArtistMusicRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface   $redisDAO   RedisDAO.
     * @param   RemoteInterface     $remote     Remote.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        RemoteInterface $remote
    );

    /**
     * Prepare AbstractArtistMusic.
     * @param   EntityId                $chartRankingItemId     The id of ChartRankingItem.
     * @param   ChartArtist             $chartArtist            ChartArtist.
     * @param   ChartMusic              $chartMusic             ChartMusic.
     * @param   ApiName                 $apiName                Name of api.
     * @param   AbstractRequestSender   $requestSender          RequestSender.
     */
    public function prepare(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName,
        AbstractRequestSender $requestSender
    );

    /**
     * Get AbstractArtistMusicEntity.
     * @param   AbstractArtistMusicBusinessId   $businessId     Business id of AbstractArtistMusic.
     * @return  AbstractArtistMusicEntity
     */
    public function get(AbstractArtistMusicBusinessId $businessId);

}
