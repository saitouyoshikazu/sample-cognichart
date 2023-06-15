<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\Artist\ArtistRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Artist\ArtistFactoryInterface;
use App\Domain\EntityId;
use App\Domain\DomainPaginator;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\Artist\ArtistBusinessId;
use App\Domain\Artist\ArtistSpecification;
use App\Domain\Artist\ArtistEntity;
use App\Infrastructure\BuilderContainerInterface;
use App\Infrastructure\Eloquents\Artist;
use App\Infrastructure\Eloquents\ProvisionedArtist;

class ArtistRepository extends DomainRepository implements ArtistRepositoryInterface
{

    private $artistFactory;

    public function __construct(
        RedisDAOInterface $redisDAO,
        ArtistFactoryInterface $artistFactory
    ) {
        parent::__construct($redisDAO);
        $this->artistFactory = $artistFactory;
    }

    public function findProvision(EntityId $id)
    {
        $row = ProvisionedArtist::find($id->value());
        if (empty($row)) {
            return null;
        }
        $artistEntity = $this->artistFactory->create(
            $row->id,
            $row->itunes_artist_id,
            $row->artist_name
        );
        return $artistEntity;
    }

    public function findRelease(EntityId $id)
    {
        $row = Artist::find($id->value());
        if (empty($row)) {
            return null;
        }
        $artistEntity = $this->artistFactory->create(
            $row->id,
            $row->itunes_artist_id,
            $row->artist_name
        );
        return $artistEntity;
    }

    public function findWithCache(EntityId $entityId, ArtistSpecification $artistSpecification)
    {
        return $artistSpecification->findWithCache($entityId, $this);
    }

    public function getProvision(ArtistBusinessId $artistBusinessId, EntityId $excludeId = null)
    {
        $provisionedArtist = ProvisionedArtist::businessId($artistBusinessId->iTunesArtistId()->value());
        if (!empty($excludeId)) {
            $provisionedArtist = $provisionedArtist->excludeId($excludeId->value());
        }
        $row = $provisionedArtist->first();
        if (empty($row)) {
            return null;
        }
        $artistEntity = $this->artistFactory->create(
            $row->id,
            $row->itunes_artist_id,
            $row->artist_name
        );
        return $artistEntity;
    }

    public function getRelease(ArtistBusinessId $artistBusinessId, EntityId $excludeId = null)
    {
        $artist = Artist::businessId($artistBusinessId->iTunesArtistId()->value());
        if (!empty($excludeId)) {
            $artist = $artist->excludeId($excludeId->value());
        }
        $row = $artist->first();
        if (empty($row)) {
            return null;
        }
        $artistEntity = $this->artistFactory->create(
            $row->id,
            $row->itunes_artist_id,
            $row->artist_name
        );
        return $artistEntity;
    }

    public function refreshCachedEntity(EntityId $entityId, ArtistSpecification $artistSpecification)
    {
        $artistSpecification->refreshCachedEntity($entityId, $this);
    }

    public function register(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification)
    {
        $artistSpecification->register($artistEntity, $this);
        $parameters = [
            'id'                =>  $artistEntity->id()->value(),
            'itunes_artist_id'  =>  $artistEntity->iTunesArtistId()->value(),
            'artist_name'       =>  $artistEntity->artistName()->value()
        ];
        $provisionedArtist = new ProvisionedArtist();
        if (!$provisionedArtist->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    public function modifyProvision(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification)
    {
        $artistSpecification->modifyProvision($artistEntity, $this);
        $parameters = [
            'itunes_artist_id'  =>  $artistEntity->iTunesArtistId()->value(),
            'artist_name'       =>  $artistEntity->artistName()->value()
        ];
        $result = ProvisionedArtist::find($artistEntity->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        return true;
    }

    public function delete(EntityId $id, ArtistSpecification $artistSpecification)
    {
        $artistSpecification->delete($id, $this);
        $result = ProvisionedArtist::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return true;
    }

    public function release(EntityId $id, ArtistSpecification $artistSpecification)
    {
        $releaseTarget = $artistSpecification->release($id, $this);
        $result = ProvisionedArtist::destroy($releaseTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $parameters = [
            'id'                =>  $releaseTarget->id()->value(),
            'itunes_artist_id'  =>  $releaseTarget->iTunesArtistId()->value(),
            'artist_name'       =>  $releaseTarget->artistName()->value()
        ];
        $artist = new Artist();
        if (!$artist->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    public function modifyRelease(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification)
    {
        $artistSpecification->modifyRelease($artistEntity, $this);
        $parameters = [
            'itunes_artist_id'  =>  $artistEntity->iTunesArtistId()->value(),
            'artist_name'       =>  $artistEntity->artistName()->value()
        ];
        $result = Artist::find($artistEntity->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        return true;
    }

    public function rollback(EntityId $id, ArtistSpecification $artistSpecification)
    {
        $rollbackTarget = $artistSpecification->rollback($id, $this);
        $result = Artist::destroy($rollbackTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $parameters = [
            'id'                =>  $rollbackTarget->id()->value(),
            'itunes_artist_id'  =>  $rollbackTarget->iTunesArtistId()->value(),
            'artist_name'       =>  $rollbackTarget->artistName()->value()
        ];
        $provisionedArtist = new ProvisionedArtist();
        if (!$provisionedArtist->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    public function provisionedEntities(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification)
    {
        $artistEntities = [];
        $rows = $artistSpecification
            ->buildQuery($iTunesArtistId, $artistName, 'ProvisionedArtist', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('artist_name', 'asc')
            ->get();
        if (empty($rows)) {
            return $artistEntities;
        }
        foreach ($rows AS $row) {
            $artistEntities[] = $this->artistFactory->create(
                $row->id,
                $row->itunes_artist_id,
                $row->artist_name
            );
        }
        return $artistEntities;
    }

    public function releasedEntities(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification)
    {
        $artistEntities = [];
        $rows = $artistSpecification
            ->buildQuery($iTunesArtistId, $artistName, 'Artist', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('artist_name', 'asc')
            ->get();
        if (empty($rows)) {
            return $artistEntities;
        }
        foreach ($rows AS $row) {
            $artistEntities[] = $this->artistFactory->create(
                $row->id,
                $row->itunes_artist_id,
                $row->artist_name
            );
        }
        return $artistEntities;
    }

    public function provisionedPaginator(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification)
    {
        $artistEntities = [];
        $rows = $artistSpecification
            ->buildQuery($iTunesArtistId, $artistName, 'ProvisionedArtist', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('artist_name', 'asc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $artistEntities[] = $this->artistFactory->create(
                $row->id,
                $row->itunes_artist_id,
                $row->artist_name
            );
        }
        return new DomainPaginator($artistEntities, $rows);
    }

    public function releasedPaginator(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification)
    {
        $artistEntities = [];
        $rows = $artistSpecification
            ->buildQuery($iTunesArtistId, $artistName, 'Artist', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('artist_name', 'asc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $artistEntities[] = $this->artistFactory->create(
                $row->id,
                $row->itunes_artist_id,
                $row->artist_name
            );
        }
        return new DomainPaginator($artistEntities, $rows);
    }

    public function builderWithITunesArtistId(BuilderContainerInterface $builderContainer = null, ITunesArtistId $iTunesArtistId = null, string $eloquentName)
    {
        $builderContainer = $this->initBuilder($builderContainer, $eloquentName);
        $builder = $builderContainer->get($eloquentName);
        if (!empty($iTunesArtistId)) {
            $builder = $builder->iTunesArtistId($iTunesArtistId->value());
        }
        $builderContainer->set($eloquentName, $builder);
        return $builderContainer;
    }

    public function builderWithArtistName(BuilderContainerInterface $builderContainer = null, ArtistName $artistName = null, string $eloquentName)
    {
        $builderContainer = $this->initBuilder($builderContainer, $eloquentName);
        $builder = $builderContainer->get($eloquentName);
        if (!empty($artistName)) {
            $builder = $builder->artistNameLike($artistName->value());
        }
        $builderContainer->set($eloquentName, $builder);
        return $builderContainer;
    }

    protected function idExisting(EntityId $id)
    {
        $row = Artist::find($id->value());
        if (!empty($row)) {
            return true;
        }
        $row = ProvisionedArtist::find($id->value());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

}
