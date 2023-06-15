<?php

namespace App\Application\Artist;
use DB;
use Event;
use App\Domain\Artist\ArtistRepositoryInterface;
use App\Domain\Artist\ArtistFactoryInterface;
use App\Application\DXO\ArtistDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\Artist\ArtistSpecification;
use App\Events\ArtistModified;
use App\Events\ArtistDeleted;
use App\Events\ArtistRollbacked;

class ArtistApplication implements ArtistApplicationInterface
{

    private $artistRepository;
    private $artistFactory;

    public function __construct(
        ArtistRepositoryInterface $artistRepository,
        ArtistFactoryInterface $artistFactory
    )  {
        $this->artistRepository = $artistRepository;
        $this->artistFactory = $artistFactory;
    }

    public function find(ArtistDXO $artistDXO)
    {
        $phase = $artistDXO->getPhase();
        $entityId = $artistDXO->getEntityId();
        if (empty($phase) || empty($entityId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->artistRepository->findRelease($entityId);
        } else if ($phase->isProvisioned()) {
            return $this->artistRepository->findProvision($entityId);
        }
        return null;
    }

    public function get(ArtistDXO $artistDXO)
    {
        $phase = $artistDXO->getPhase();
        $artistBusinessId = $artistDXO->getBusinessId();
        if (empty($phase) || empty($artistBusinessId)) {
            return null;
        }
        if ($phase->isReleased()) {
            return $this->artistRepository->getRelease($artistBusinessId);
        } else if ($phase->isProvisioned()) {
            return $this->artistRepository->getProvision($artistBusinessId);
        }
        return null;
    }

    public function register(ArtistDXO $artistDXO)
    {
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($iTunesArtistId) || empty($artistName)) {
            return false;
        }
        $artistEntity = $this->artistFactory->create(
            $this->artistRepository->createId()->value(),
            $iTunesArtistId->value(),
            $artistName->value()
        );
        if (empty($artistEntity)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->artistRepository->register($artistEntity, new ArtistSpecification());
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

    public function modify(ArtistDXO $artistDXO)
    {
        $phase = $artistDXO->getPhase();
        $entityId = $artistDXO->getEntityId();
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($phase) || empty($entityId) || empty($iTunesArtistId) || empty($artistName)) {
            return false;
        }

        $artistEntity = null;
        if ($phase->isReleased()) {
            $artistEntity = $this->artistRepository->findRelease($entityId);
        } else if ($phase->isProvisioned()) {
            $artistEntity = $this->artistRepository->findProvision($entityId);
        } else {
            return false;
        }
        if (empty($artistEntity)) {
            return false;
        }
        $oldITunesArtistId = $artistEntity->iTunesArtistId();
        $artistEntity
            ->setITunesArtistId($iTunesArtistId)
            ->setArtistName($artistName);

        DB::beginTransaction();
        try {
            $result = false;
            if ($phase->isReleased()) {
                $result = $this->artistRepository->modifyRelease($artistEntity, new ArtistSpecification());
            } else {
                $result = $this->artistRepository->modifyProvision($artistEntity, new ArtistSpecification());
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
            new ArtistModified(
                $artistEntity->id()->value(),
                $oldITunesArtistId->value(),
                $artistEntity->iTunesArtistId()->value()
            )
        );
        return true;
    }

    public function delete(ArtistDXO $artistDXO)
    {
        $entityId = $artistDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        $deletedArtistEntity = $this->artistRepository->findProvision($entityId);
        DB::beginTransaction();
        try {
            $result = $this->artistRepository->delete($entityId, new ArtistSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        if (!empty($deletedArtistEntity)) {
            Event::dispatch(
                new ArtistDeleted(
                    $deletedArtistEntity->id()->value(),
                    $deletedArtistEntity->iTunesArtistId()->value()
                )
            );
        }
        return true;
    }

    public function release(ArtistDXO $artistDXO)
    {
        $entityId = $artistDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->artistRepository->release($entityId, new ArtistSpecification());
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

    public function rollback(ArtistDXO $artistDXO)
    {
        $entityId = $artistDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        DB::beginTransaction();
        try {
            $result = $this->artistRepository->rollback($entityId, new ArtistSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();
        $rollbackedArtistEntity = $this->artistRepository->findProvision($entityId);
        if (empty($rollbackedArtistEntity)) {
            return false;
        }
        Event::dispatch(
            new ArtistRollbacked(
                $rollbackedArtistEntity->id()->value(),
                $rollbackedArtistEntity->iTunesArtistId()->value()
            )
        );
        return true;
    }

    public function refreshCachedEntity(ArtistDXO $artistDXO)
    {
        $entityId = $artistDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        $this->artistRepository->refreshCachedEntity($entityId, new ArtistSpecification());
        return true;
    }

    public function provisionedEntities(ArtistDXO $artistDXO)
    {
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($artistName) && empty($iTunesArtistId)) {
            return [];
        }
        return $this->artistRepository->provisionedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
    }

    public function releasedEntities(ArtistDXO $artistDXO)
    {
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($artistName) && empty($iTunesArtistId)) {
            return [];
        }
        return $this->artistRepository->releasedEntities($iTunesArtistId, $artistName, new ArtistSpecification());
    }

    public function provisionedPaginator(ArtistDXO $artistDXO)
    {
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($artistName) && empty($iTunesArtistId)) {
            return null;
        }
        return $this->artistRepository->provisionedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
    }

    public function releasedPaginator(ArtistDXO $artistDXO)
    {
        $iTunesArtistId = $artistDXO->getITunesArtistId();
        $artistName = $artistDXO->getArtistName();
        if (empty($artistName) && empty($iTunesArtistId)) {
            return null;
        }
        return $this->artistRepository->releasedPaginator($iTunesArtistId, $artistName, new ArtistSpecification());
    }

}
