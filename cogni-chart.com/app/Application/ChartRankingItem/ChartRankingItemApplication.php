<?php

namespace App\Application\ChartRankingItem;
use App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface;
use App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface;
use DB;
use Event;
use App\Application\DXO\ChartRankingItemDXO;
use App\Domain\ChartRankingItem\ChartRankingItemSpecification;
use App\Events\ChartRankingItemCreated;
use App\Events\ChartRankingItemModified;

class ChartRankingItemApplication implements ChartRankingItemApplicationInterface
{

    private $chartRankingItemRepository;
    private $chartRankingItemFactory;

    public function __construct(
        ChartRankingItemRepositoryInterface $chartRankingItemRepository,
        ChartRankingItemFactoryInterface $chartRankingItemFactory
    ) {
        $this->chartRankingItemRepository = $chartRankingItemRepository;
        $this->chartRankingItemFactory = $chartRankingItemFactory;
    }

    public function exists(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $chartRankingItemBusinessId = $chartRankingItemDXO->getBusinessId();
        if (empty($chartRankingItemBusinessId)) {
            return false;
        }
        $chartRankingItemEntity = $this->chartRankingItemRepository->get($chartRankingItemBusinessId);
        if (empty($chartRankingItemEntity)) {
            return false;
        }
        return true;
    }

    public function find(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $entityId = $chartRankingItemDXO->getEntityId();
        if (empty($entityId)) {
            return null;
        }
        return $this->chartRankingItemRepository->find($entityId);
    }

    public function get(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $chartRankingItemBusinessId = $chartRankingItemDXO->getBusinessId();
        if (empty($chartRankingItemBusinessId)) {
            return null;
        }
        return $this->chartRankingItemRepository->get($chartRankingItemBusinessId);
    }

    public function register(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $chartArtist = $chartRankingItemDXO->getChartArtist();
        $chartMusic = $chartRankingItemDXO->getChartMusic();
        $artistId = $chartRankingItemDXO->getArtistId();
        $musicId = $chartRankingItemDXO->getMusicId();
        if (empty($chartMusic)) {
            return false;
        }
        $artistIdValue = null;
        if (!empty($artistId)) {
            $artistIdValue = $artistId->value();
        }
        $musicIdValue = null;
        if (!empty($musicId)) {
            $musicIdValue = $musicId->value();
        }
        $chartRankingItemEntity = $this->chartRankingItemFactory->create(
            $this->chartRankingItemRepository->createId()->value(),
            $chartArtist->value(),
            $chartMusic->value(),
            $artistIdValue,
            $musicIdValue
        );
        if (empty($chartRankingItemEntity)) {
            return false;
        }

        DB::beginTransaction();
        try {
            $result = $this->chartRankingItemRepository->register($chartRankingItemEntity, new ChartRankingItemSpecification());
            if ($result === false) {
                DB::rollback();
                return false;
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
        DB::commit();

        if (empty($chartRankingItemEntity->artistId()) || empty($chartRankingItemEntity->musicId())) {
            Event::dispatch(
                new ChartRankingItemCreated(
                    $chartRankingItemEntity->id()->value(),
                    $chartRankingItemEntity->chartArtist()->value(),
                    $chartRankingItemEntity->chartMusic()->value()
                )
            );
        }

        return true;
    }

    public function modify(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $entityId = $chartRankingItemDXO->getEntityId();
        $chartArtist = $chartRankingItemDXO->getChartArtist();
        $chartMusic = $chartRankingItemDXO->getChartMusic();
        $artistId = $chartRankingItemDXO->getArtistId();
        $musicId = $chartRankingItemDXO->getMusicId();
        if (empty($entityId) || empty($chartArtist) || empty($chartMusic)) {
            return false;
        }

        $chartRankingItemEntity = $this->chartRankingItemRepository->find($entityId);
        if (empty($chartRankingItemEntity)) {
            return false;
        }
        $oldChartArtist = $chartRankingItemEntity->chartArtist();
        $oldChartMusic = $chartRankingItemEntity->chartMusic();
        $chartRankingItemEntity
            ->setChartArtist($chartArtist)
            ->setChartMusic($chartMusic)
            ->setArtistId($artistId)
            ->setMusicId($musicId);
        DB::beginTransaction();
        try {
            $result = $this->chartRankingItemRepository->modify($chartRankingItemEntity, new ChartRankingItemSpecification());
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
            new ChartRankingItemModified(
                $chartRankingItemEntity->id()->value(),
                $oldChartArtist->value(),
                $oldChartMusic->value()
            )
        );
        return true;
    }

    public function refreshCachedEntity(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $entityId = $chartRankingItemDXO->getEntityId();
        if (empty($entityId)) {
            return false;
        }
        $this->chartRankingItemRepository->refreshCachedEntity($entityId, new ChartRankingItemSpecification());
        return true;
    }

    public function detachArtist(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $artistId = $chartRankingItemDXO->getArtistId();
        if (empty($artistId)) {
            return false;
        }
        $chartRankingItemEntities = $this->chartRankingItemRepository->entities(null, null, $artistId, null, new ChartRankingItemSpecification());
        if (empty($chartRankingItemEntities)) {
            return true;
        }
        DB::beginTransaction();
        try {
            foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
                $chartRankingItemEntity->setArtistId(null);
                $result = $this->chartRankingItemRepository->modify($chartRankingItemEntity, new ChartRankingItemSpecification());
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
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            Event::dispatch(
                new ChartRankingItemModified(
                    $chartRankingItemEntity->id()->value(),
                    $chartRankingItemEntity->chartArtist()->value(),
                    $chartRankingItemEntity->chartMusic()->value()
                )
            );
        }
        return true;
    }

    public function detachMusic(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $musicId = $chartRankingItemDXO->getMusicId();
        if (empty($musicId)) {
            return false;
        }
        $chartRankingItemEntities = $this->chartRankingItemRepository->entities(null, null, null, $musicId, new ChartRankingItemSpecification());
        if (empty($chartRankingItemEntities)) {
            return true;
        }
        DB::beginTransaction();
        try {
            foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
                $chartRankingItemEntity->setMusicId(null);
                $result = $this->chartRankingItemRepository->modify($chartRankingItemEntity, new ChartRankingItemSpecification());
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
        foreach ($chartRankingItemEntities AS $chartRankingItemEntity) {
            Event::dispatch(
                new ChartRankingItemModified(
                    $chartRankingItemEntity->id()->value(),
                    $chartRankingItemEntity->chartArtist()->value(),
                    $chartRankingItemEntity->chartMusic()->value()
                )
            );
        }
        return true;
    }

    public function notAttachedPaginator(ChartRankingItemDXO $chartRankingItemDXO)
    {
        $chartArtist = $chartRankingItemDXO->getChartArtist();
        $chartMusic = $chartRankingItemDXO->getChartMusic();
        return $this->chartRankingItemRepository->notAttachedPaginator($chartArtist, $chartMusic, new ChartRankingItemSpecification());
    }

}
