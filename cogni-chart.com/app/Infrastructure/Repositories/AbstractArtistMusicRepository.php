<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ValueObjects\ApiName;
use App\Domain\AbstractArtistMusic\Strategy\AbstractRequestSender;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicBusinessId;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicEntity;

class AbstractArtistMusicRepository extends DomainRepository implements AbstractArtistMusicRepositoryInterface
{

    private $remote;

    public function __construct(
        RedisDAOInterface $redisDAO,
        RemoteInterface $remote
    ) {
        parent::__construct($redisDAO);
        $this->remote = $remote;
    }

    public function prepare(
        EntityId $chartRankingItemId,
        ChartArtist $chartArtist,
        ChartMusic $chartMusic,
        ApiName $apiName,
        AbstractRequestSender $requestSender
    ) {
        $response = $requestSender->send($this->remote, $chartArtist, $chartMusic);
        if (empty($response)) {
            return false;
        }
        $abstractArtistMusicEntity = new AbstractArtistMusicEntity($chartRankingItemId, $apiName);
        $abstractArtistMusicEntity->setResponse($response);
        return $this->storeCache($abstractArtistMusicEntity, AbstractArtistMusicEntity::class);
    }

    public function get(AbstractArtistMusicBusinessId $businessId)
    {
        $abstractArtistMusicEntity = $this->findCache($businessId, AbstractArtistMusicEntity::class);
        return $abstractArtistMusicEntity;
    }

    protected function idExisting(EntityId $id)
    {
        return false;
    }

}
