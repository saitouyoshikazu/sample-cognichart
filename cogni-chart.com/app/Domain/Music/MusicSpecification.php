<?php

namespace App\Domain\Music;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;

class MusicSpecification
{

    public function findWithCache(EntityId $entityId, MusicRepositoryInterface $musicRepository)
    {
        $musicEntity = $musicRepository->findCacheById($entityId, MusicEntity::class);
        if (!empty($musicEntity)) {
            return $musicEntity;
        }
        $musicEntity = $musicRepository->findRelease($entityId);
        if (empty($musicEntity)) {
            return null;
        }
        $musicRepository->storeCacheById($musicEntity, MusicEntity::class);
        return $musicEntity;
    }

    public function refreshCachedEntity(EntityId $entityId, MusicRepositoryInterface $musicRepository)
    {
        $musicRepository->deleteCacheById($entityId, MusicEntity::class);
        $musicEntity = $musicRepository->findRelease($entityId);
        if (empty($musicEntity)) {
            return;
        }
        $musicRepository->storeCacheById($musicEntity, MusicEntity::class);
    }

    public function register(MusicEntity $musicEntity, MusicRepositoryInterface $musicRepository)
    {
        $releasedMusicEntity = $musicRepository->findRelease($musicEntity->id());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't register to provision MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->findProvision($musicEntity->id());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't register to provision MusicEntity because provisioned Music is already existing.");
        }
        $releasedMusicEntity = $musicRepository->getRelease($musicEntity->businessId());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't register to provision MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->getProvision($musicEntity->businessId());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't register to provision MusicEntity because provisioned Music is already existing.");
        }
    }

    public function modifyProvision(MusicEntity $musicEntity, MusicRepositoryInterface $musicRepository)
    {
        $releasedMusicEntity = $musicRepository->findRelease($musicEntity->id());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't modify provisioned MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->findProvision($musicEntity->id());
        if (empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't modify provisioned MusicEntity because provisioned Music doesn't exist.");
        }
        $releasedMusicEntity = $musicRepository->getRelease($musicEntity->businessId());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't modify provisioned MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->getProvision($musicEntity->businessId(), $musicEntity->id());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't modify provisioned MusicEntity because provisioned Music is already existing.");
        }
    }

    public function delete(EntityId $id, MusicRepositoryInterface $musicRepository)
    {
        $provisionedMusicEntity = $musicRepository->findProvision($id);
        if (empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't delete provisioned MusicEntity because provisioned Music doesn't exist.");
        }
    }

    public function release(EntityId $id, MusicRepositoryInterface $musicRepository)
    {
        $releasedMusicEntity = $musicRepository->findRelease($id);
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't release provisioned MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->findProvision($id);
        if (empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't release provisioned MusicEntity because provisioned Music doesn't exist.");
        }

        $releaseTarget = $provisionedMusicEntity;

        $releasedMusicEntity = $musicRepository->getRelease($releaseTarget->businessId());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't release provisioned MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->getProvision($releaseTarget->businessId(), $releaseTarget->id());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't release provisioned MusicEntity because same provisioned Music is already existing.");
        }
        return $releaseTarget;
    }

    public function modifyRelease(MusicEntity $musicEntity, MusicRepositoryInterface $musicRepository)
    {
        $releasedMusicEntity = $musicRepository->findRelease($musicEntity->id());
        if (empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't modify released MusicEntity because released Music doesn't exist.");
        }
        $provisionedMusicEntity = $musicRepository->findProvision($musicEntity->id());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't modify released MusicEntity because provisioned Music is already existing.");
        }
        $releasedMusicEntity = $musicRepository->getRelease($musicEntity->businessId(), $musicEntity->id());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't modify released MusicEntity because released Music is already existing.");
        }
        $provisionedMusicEntity = $musicRepository->getProvision($musicEntity->businessId());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't modify released MusicEntity because provisioned Music is already existing.");
        }
    }

    public function rollback(EntityId $id, MusicRepositoryInterface $musicRepository)
    {
        $provisionedMusicEntity = $musicRepository->findProvision($id);
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't rollback MusicEntity because provisioned Music is already existing.");
        }
        $releasedMusicEntity = $musicRepository->findRelease($id);
        if (empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't rollback MusicEntity because released Music doesn't exist.");
        }

        $rollbackTarget = $releasedMusicEntity;

        $provisionedMusicEntity = $musicRepository->getProvision($rollbackTarget->businessId());
        if (!empty($provisionedMusicEntity)) {
            throw new MusicException("Couldn't rollback MusicEntity because provisioned Music is already existing.");
        }
        $releasedMusicEntity = $musicRepository->getRelease($rollbackTarget->businessId(), $rollbackTarget->id());
        if (!empty($releasedMusicEntity)) {
            throw new MusicException("Couldn't rollback MusicEntity because same released Music is existing.");
        }
        return $rollbackTarget;
    }

    public function buildQuery(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, string $eloquentName, MusicRepositoryInterface $musicRepository)
    {
        $builderContainer = $musicRepository->builderWithITunesArtistId(null, $iTunesArtistId, $eloquentName);
        $builderContainer = $musicRepository->builderWithMusicTitle($builderContainer, $musicTitle, $eloquentName);
        return $builderContainer->get($eloquentName);
    }

}
