<?php

namespace App\Application\Music;
use DB;
use Event;
use App\Domain\Music\MusicRepositoryInterface;
use App\Domain\Music\MusicFactoryInterface;
use App\Domain\Music\MusicServiceInterface;
use App\Application\DXO\MusicDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;
use App\Domain\Music\MusicSpecification;
use App\Domain\Music\MusicEntity;
use App\Domain\Music\MusicException;
use App\Events\MusicRegistered;
use App\Events\MusicModified;
use App\Events\MusicDeleted;
use App\Events\MusicRollbacked;

class MusicApplication implements MusicApplicationInterface
{

    private $musicRepository;
    private $musicFactory;
    private $musicService;

    public function __construct(
        MusicRepositoryInterface $musicRepository,
        MusicFactoryInterface $musicFactory,
        MusicServiceInterface $musicService
    )  {
        $this->musicRepository = $musicRepository;
        $this->musicFactory = $musicFactory;
        $this->musicService = $musicService;
    }

    public function find(MusicDXO $musicDXO)
    {
        $phase = $musicDXO->getPhase();
        $entityId = $musicDXO->getEntityId();
        if (empty($phase) || empty($entityId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->musicRepository->findWithCache($entityId, new MusicSpecification());
        } else if ($phase->isProvisioned()) {
            return $this->musicRepository->findProvision($entityId);
        }
        return null;
    }

    public function get(MusicDXO $musicDXO)
    {
        $phase = $musicDXO->getPhase();
        $musicBusinessId = $musicDXO->getBusinessId();
        if (empty($phase) || empty($musicBusinessId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->musicRepository->getRelease($musicBusinessId);
        } else if ($phase->isProvisioned()) {
            return $this->musicRepository->getProvision($musicBusinessId);
        }
        return null;
    }

    public function register(MusicDXO $musicDXO)
    {
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        $musicTitle = $musicDXO->getMusicTitle();
        $iTunesBaseUrl = $musicDXO->getITunesBaseUrl();
        $promotionVideoUrl = $musicDXO->getPromotionVideoUrl();
        $thumbnailUrl = $musicDXO->getThumbnailUrl();
        if (empty($iTunesArtistId) || empty($musicTitle)) {
            return false;
        }
        $iTunesBaseUrlValue = null;
        if (!empty($iTunesBaseUrl)) {
            $iTunesBaseUrlValue = $iTunesBaseUrl->value();
        }
        $promotionVideoUrlValue = null;
        $thumbnailUrlValue = null;
        if (!empty($promotionVideoUrl)) {
            $promotionVideoUrlValue = $promotionVideoUrl->value();
            $thumbnailUrlValue = !empty($thumbnailUrl) ? $thumbnailUrl->value() : null;
        }
        $musicEntity = $this->musicFactory->create(
            $this->musicRepository->createId()->value(),
            $iTunesArtistId->value(),
            $musicTitle->value(),
            $iTunesBaseUrlValue,
            $promotionVideoUrlValue,
            $thumbnailUrlValue
        );
        if (empty($musicEntity)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->musicRepository->register($musicEntity, new MusicSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        Event::dispatch(
            new MusicRegistered(
                $musicEntity->id()->value()
            )
        );
        return true;
    }

    public function modify(MusicDXO $musicDXO)
    {
        $phase = $musicDXO->getPhase();
        $entityId = $musicDXO->getEntityId();
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        $musicTitle = $musicDXO->getMusicTitle();
        $iTunesBaseUrl = $musicDXO->getITunesBaseUrl();
        $promotionVideoUrl = $musicDXO->getPromotionVideoUrl();
        $thumbnailUrl = $musicDXO->getThumbnailUrl();
        if (empty($phase) || empty($entityId) || empty($iTunesArtistId) || empty($musicTitle)) {
            return false;
        }
        $thumbnailUrl = empty($promotionVideoUrl) ? null : $thumbnailUrl;

        $musicEntity = null;
        if ($phase->isReleased()) {
            $musicEntity = $this->musicRepository->findRelease($entityId);
        } else if ($phase->isProvisioned()) {
            $musicEntity = $this->musicRepository->findProvision($entityId);
        } else {
            return false;
        }
        if (empty($musicEntity)) {
            return false;
        }
        $oldITunesArtistId = $musicEntity->iTunesArtistId();
        $oldMusicTitle = $musicEntity->musicTitle();
        $musicEntity
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle)
            ->setITunesBaseUrl($iTunesBaseUrl)
            ->setPromotionVideoUrl($promotionVideoUrl)
            ->setThumbnailUrl($thumbnailUrl);

        DB::beginTransaction();
        try {
            $result = false;
            if ($phase->isReleased()) {
                $result = $this->musicRepository->modifyRelease($musicEntity, new MusicSpecification());
            } else {
                $result = $this->musicRepository->modifyProvision($musicEntity, new MusicSpecification());
            }
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        Event::dispatch(
            new MusicModified(
                $musicEntity->id()->value(),
                $oldITunesArtistId->value(),
                $oldMusicTitle->value()
            )
        );
        return true;
    }

    public function delete(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->musicRepository->delete($entityId, new MusicSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        Event::dispatch(
            new MusicDeleted(
                $entityId->value()
            )
        );
        return true;
    }

    public function release(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->musicRepository->release($entityId, new MusicSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    public function rollback(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->musicRepository->rollback($entityId, new MusicSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        $rollbackedMusicEntity = $this->musicRepository->findProvision($entityId);
        if (empty($rollbackedMusicEntity)) {
            return false;
        }
        Event::dispatch(
            new MusicRollbacked(
                $rollbackedMusicEntity->id()->value(),
                $rollbackedMusicEntity->iTunesArtistId()->value(),
                $rollbackedMusicEntity->musicTitle()->value()
            )
        );
        return true;
    }

    public function refreshCachedEntity(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        $this->musicRepository->refreshCachedEntity($entityId, new MusicSpecification());
        return true;
    }

    public function assignPromotionVideo(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        $artistName = $musicDXO->getArtistName();
        $musicTitle = $musicDXO->getMusicTitle();
        if (empty($entityId) || empty($artistName) || empty($musicTitle)) {
            return false;
        }
        $phase = new Phase(Phase::provisioned);
        $musicEntity = $this->musicRepository->findProvision($entityId);
        if (empty($musicEntity)) {
            $phase = new Phase(Phase::released);
            $musicEntity = $this->musicRepository->findRelease($entityId);
        }
        if (empty($musicEntity)) {
            return false;
        }
        if (!empty($musicEntity->promotionVideoUrl()) || !empty($musicEntity->thumbnailUrl())) {
            return true;
        }
        $promotionVideoRow = $this->musicService->searchPromotionVideo($artistName, $musicTitle);
        if (empty($promotionVideoRow)) {
            return true;
        }

        $iTunesBaseUrlValue = null;
        if (!empty($musicEntity->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $musicEntity->iTunesBaseUrl()->value();
        }

        $musicDXO = new MusicDXO();
        $musicDXO->modify(
            $phase->value(),
            $musicEntity->id()->value(),
            $musicEntity->iTunesArtistId()->value(),
            $musicEntity->musicTitle()->value(),
            $iTunesBaseUrlValue,
            $promotionVideoRow['url'],
            $promotionVideoRow['thumbnail_url']
        );
        return $this->modify($musicDXO);
    }

    public function checkPromotionVideo(MusicDXO $musicDXO)
    {
        $checkPromotionVideoConditions = $musicDXO->getCheckPromotionVideoConditions();
        if (empty($checkPromotionVideoConditions)) {
            return true;
        }

        $musicEntities = $this->musicRepository->checkPromotionVideoList($checkPromotionVideoConditions);
        if (empty($musicEntities)) {
            return true;
        }
        foreach ($musicEntities AS $musicEntity) {
            if ($this->musicService->checkPromotionVideo($musicEntity)) {
                sleep(5);
                continue;
            }
            $phase = $this->musicRepository->getPhase($musicEntity->id());
            if (empty($phase)) {
                sleep(5);
                continue;
            }
            $musicEntity->setPromotionVideoUrl(null)->setThumbnailUrl(null);
            DB::beginTransaction();
            try {
                if ($phase->isReleased()) {
                    $result = $this->musicRepository->modifyRelease($musicEntity, new MusicSpecification());
                } else {
                    $result = $this->musicRepository->modifyProvision($musicEntity, new MusicSpecification());
                }
                if ($result === false) {
                    DB::rollback();
                    sleep(5);
                    continue;
                }
                $result = $this->musicRepository->registerPromotionVideoBrokenLink($musicEntity);
                if ($result === false) {
                    DB::rollback();
                    sleep(5);
                    continue;
                }
                $this->musicRepository->deleteCacheById($musicEntity->id(), MusicEntity::class);
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
            DB::commit();
            sleep(5);
        }
        return true;
    }

    public function promotionVideoBrokenLinks(MusicDXO $musicDXO)
    {
        $iTunesArtistIds = $musicDXO->getItunesArtistIds();
        $musicIds = [];
        if (!empty($iTunesArtistIds)) {
            foreach ($iTunesArtistIds AS $iTunesArtistId) {
                $musicEntities = $this->musicRepository->provisionedEntities($iTunesArtistId, null, new MusicSpecification());
                if (!empty($musicEntities)) {
                    foreach ($musicEntities AS $musicEntity) {
                        $musicIds[] = $musicEntity->id();
                    }
                }
                $musicEntities = $this->musicRepository->releasedEntities($iTunesArtistId, null, new MusicSpecification());
                if (!empty($musicEntities)) {
                    foreach ($musicEntities AS $musicEntity) {
                        $musicIds[] = $musicEntity->id();
                    }
                }
            }
        }
        return $this->musicRepository->promotionVideoBrokenLinks($musicIds);
    }

    public function deletePromotionVideoBrokenLink(MusicDXO $musicDXO)
    {
        $entityId = $musicDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->musicRepository->deletePromotionVideoBrokenLink($entityId);
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        return true;
    }

    public function deleteWithITunesArtistId(MusicDXO $musicDXO)
    {
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        if (empty($iTunesArtistId)) {
            return false;
        }
        $releasedMusicEntities = $this->musicRepository->releasedEntities($iTunesArtistId, null, new MusicSpecification());
        if (!empty($releasedMusicEntities)) {
            foreach ($releasedMusicEntities AS $releasedMusicEntity) {
                $dxo = new MusicDXO();
                $dxo->rollback($releasedMusicEntity->id()->value());
                $result = $this->rollback($dxo);
                if ($result === false) {
                    throw new MusicException("Failed to rollback Music. iTunesArtistId: {$iTunesArtistId->value()}, MusicId: {$releasedMusicEntity->id()->value()}");
                }
            }
        }

        $provisionedMusicEntities = $this->musicRepository->provisionedEntities($iTunesArtistId, null, new MusicSpecification());
        if (!empty($provisionedMusicEntities)) {
            foreach ($provisionedMusicEntities AS $provisionedMusicEntity) {
                $dxo = new MusicDXO();
                $dxo->delete($provisionedMusicEntity->id()->value());
                $result = $this->delete($dxo);
                if ($result === false) {
                    throw new MusicException("Failed to delete Music. iTunesArtistId: {$iTunesArtistId->value()}, MusicId: {$provisionedMusicEntity->id()->value()}");
                }
            }
        }
        return true;
    }

    public function replaceITunesArtistId(MusicDXO $musicDXO)
    {
        $oldITunesArtistId = $musicDXO->getOldITunesArtistId();
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        if (empty($oldITunesArtistId) || empty($iTunesArtistId)) {
            return false;
        }
        if ($oldITunesArtistId->value() === $iTunesArtistId->value()) {
            return true;
        }
        $provisionedMusicEntities = $this->musicRepository->provisionedEntities($oldITunesArtistId, null, new MusicSpecification());
        $releasedMusicEntities = $this->musicRepository->releasedEntities($oldITunesArtistId, null, new MusicSpecification());
        DB::beginTransaction();
        try {
            foreach ($provisionedMusicEntities AS $musicEntity) {
                $musicEntity->setITunesArtistId($iTunesArtistId);
                $result = $this->musicRepository->modifyProvision($musicEntity, new MusicSpecification());
                if ($result === false) {
                    DB::rollback();
                    return false;
                }
            }
            foreach ($releasedMusicEntities AS $musicEntity) {
                $musicEntity->setITunesArtistId($iTunesArtistId);
                $result = $this->musicRepository->modifyRelease($musicEntity, new MusicSpecification());
                if ($result === false) {
                    DB::rollback();
                    return false;
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        foreach ($provisionedMusicEntities AS $musicEntity) {
            Event::dispatch(
                new MusicModified(
                    $musicEntity->id()->value(),
                    $oldITunesArtistId->value(),
                    $musicEntity->musicTitle()->value()
                )
            );
        }
        foreach ($releasedMusicEntities AS $musicEntity) {
            Event::dispatch(
                new MusicModified(
                    $musicEntity->id()->value(),
                    $oldITunesArtistId->value(),
                    $musicEntity->musicTitle()->value()
                )
            );
        }
        return true;
    }

    public function provisionedPaginator(MusicDXO $musicDXO)
    {
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        $musicTitle = $musicDXO->getMusicTitle();
        if (empty($iTunesArtistId) && empty($musicTitle)) {
            return null;
        }
        return $this->musicRepository->provisionedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
    }

    public function releasedPaginator(MusicDXO $musicDXO)
    {
        $iTunesArtistId = $musicDXO->getITunesArtistId();
        $musicTitle = $musicDXO->getMusicTitle();
        if (empty($iTunesArtistId) && empty($musicTitle)) {
            return null;
        }
        return $this->musicRepository->releasedPaginator($iTunesArtistId, $musicTitle, new MusicSpecification());
    }

}
