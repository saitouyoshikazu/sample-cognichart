<?php

namespace App\Domain\Artist;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;

class ArtistSpecification
{

    public function findWithCache(EntityId $entityId, ArtistRepositoryInterface $artistRepository)
    {
        $artistEntity = $artistRepository->findCacheById($entityId, ArtistEntity::class);
        if (!empty($artistEntity)) {
            return $artistEntity;
        }
        $artistEntity = $artistRepository->findRelease($entityId);
        if (empty($artistEntity)) {
            return null;
        }
        $artistRepository->storeCacheById($artistEntity, ArtistEntity::class);
        return $artistEntity;
    }

    public function refreshCachedEntity(EntityId $entityId, ArtistRepositoryInterface $artistRepository)
    {
        $artistRepository->deleteCacheById($entityId, ArtistEntity::class);
        $artistEntity = $artistRepository->findRelease($entityId);
        if (empty($artistEntity)) {
            return;
        }
        $artistRepository->storeCacheById($artistEntity, ArtistEntity::class);
    }

    public function register(ArtistEntity $artistEntity, ArtistRepositoryInterface $artistRepository)
    {
        $releasedArtistEntity = $artistRepository->findRelease($artistEntity->id());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't register to provision ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->findProvision($artistEntity->id());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't register to provision ArtistEntity because provisioned Artist is already existing.");
        }
        $releasedArtistEntity = $artistRepository->getRelease($artistEntity->businessId());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't register to provision ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->getProvision($artistEntity->businessId());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't register to provision ArtistEntity because provisioned Artist is already existing.");
        }
    }

    public function modifyProvision(ArtistEntity $artistEntity, ArtistRepositoryInterface $artistRepository)
    {
        $releasedArtistEntity = $artistRepository->findRelease($artistEntity->id());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't modify provisioned ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->findProvision($artistEntity->id());
        if (empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't modify provisioned ArtistEntity because provisioned Artist doesn't exist.");
        }
        $releasedArtistEntity = $artistRepository->getRelease($artistEntity->businessId());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't modify provisioned ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->getProvision($artistEntity->businessId(), $artistEntity->id());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't modify provisioned ArtistEntity because provisioned Artist is already existing.");
        }
    }

    public function delete(EntityId $id, ArtistRepositoryInterface $artistRepository)
    {
        $provisionedArtistEntity = $artistRepository->findProvision($id);
        if (empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't delete provisioned ArtistEntity because provisioned Artist doesn't exist.");
        }
    }

    public function release(EntityId $id, ArtistRepositoryInterface $artistRepository)
    {
        $releasedArtistEntity = $artistRepository->findRelease($id);
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't release provisioned ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->findProvision($id);
        if (empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't release provisioned ArtistEntity because provisioned Artist doesn't exist.");
        }

        $releaseTarget = $provisionedArtistEntity;

        $releasedArtistEntity = $artistRepository->getRelease($releaseTarget->businessId());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't release provisioned ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->getProvision($releaseTarget->businessId(), $releaseTarget->id());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't release provisioned ArtistEntity because same provisioned Artist is already existing.");
        }
        return $releaseTarget;
    }

    public function modifyRelease(ArtistEntity $artistEntity, ArtistRepositoryInterface $artistRepository)
    {
        $releasedArtistEntity = $artistRepository->findRelease($artistEntity->id());
        if (empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't modify released ArtistEntity because released Artist doesn't exist.");
        }
        $provisionedArtistEntity = $artistRepository->findProvision($artistEntity->id());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't modify released ArtistEntity because provisioned Artist is already existing.");
        }
        $releasedArtistEntity = $artistRepository->getRelease($artistEntity->businessId(), $artistEntity->id());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't modify released ArtistEntity because released Artist is already existing.");
        }
        $provisionedArtistEntity = $artistRepository->getProvision($artistEntity->businessId());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't modify released ArtistEntity because provisioned Artist is already existing.");
        }
    }

    public function rollback(EntityId $id, ArtistRepositoryInterface $artistRepository)
    {
        $provisionedArtistEntity = $artistRepository->findProvision($id);
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't rollback ArtistEntity because provisioned Artist is already existing.");
        }
        $releasedArtistEntity = $artistRepository->findRelease($id);
        if (empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't rollback ArtistEntity because released Artist doesn't exist.");
        }

        $rollbackTarget = $releasedArtistEntity;

        $provisionedArtistEntity = $artistRepository->getProvision($rollbackTarget->businessId());
        if (!empty($provisionedArtistEntity)) {
            throw new ArtistException("Couldn't rollback ArtistEntity because provisioned Artist is already existing.");
        }
        $releasedArtistEntity = $artistRepository->getRelease($rollbackTarget->businessId(), $rollbackTarget->id());
        if (!empty($releasedArtistEntity)) {
            throw new ArtistException("Couldn't rollback ArtistEntity because same released Artist is existing.");
        }
        return $rollbackTarget;
    }

    public function buildQuery(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, string $eloquentName, ArtistRepositoryInterface $artistRepository)
    {
        $builderContainer = $artistRepository->builderWithITunesArtistId(null, $iTunesArtistId, $eloquentName);
        $builderContainer = $artistRepository->builderWithArtistName($builderContainer, $artistName, $eloquentName);
        return $builderContainer->get($eloquentName);
    }

}
