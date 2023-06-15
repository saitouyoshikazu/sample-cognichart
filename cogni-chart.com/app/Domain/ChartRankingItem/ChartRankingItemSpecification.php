<?php

namespace App\Domain\ChartRankingItem;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

class ChartRankingItemSpecification
{

    private $eloquentName = 'ChartRankingItem';

    public function findWithCache(EntityId $entityId, ChartRankingItemRepositoryInterface $chartRankingItemRepository)
    {
        $chartRankingItemEntity = $chartRankingItemRepository->findCacheById($entityId, ChartRankingItemEntity::class);
        if (!empty($chartRankingItemEntity)) {
            return $chartRankingItemEntity;
        }
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        if (empty($chartRankingItemEntity)) {
            return $chartRankingItemEntity;
        }
        $chartRankingItemRepository->storeCacheById($chartRankingItemEntity, ChartRankingItemEntity::class);
        return $chartRankingItemEntity;
    }

    public function refreshCachedEntity(EntityId $entityId, ChartRankingItemRepositoryInterface $chartRankingItemRepository)
    {
        $chartRankingItemRepository->deleteCacheById($entityId, ChartRankingItemEntity::class);
        $chartRankingItemEntity = $chartRankingItemRepository->find($entityId);
        if (empty($chartRankingItemEntity)) {
            return;
        }
        $chartRankingItemRepository->storeCacheById($chartRankingItemEntity, ChartRankingItemEntity::class);
    }

    public function register(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemRepositoryInterface $chartRankingItemRepository)
    {
        $already = $chartRankingItemRepository->find($chartRankingItemEntity->id());
        if (!empty($already)) {
            throw new ChartRankingItemException("Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
        $already = $chartRankingItemRepository->get($chartRankingItemEntity->businessId());
        if (!empty($already)) {
            throw new ChartRankingItemException("Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
    }

    public function modify(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemRepositoryInterface $chartRankingItemRepository)
    {
        $already = $chartRankingItemRepository->find($chartRankingItemEntity->id());
        if (empty($already)) {
            throw new ChartRankingItemException("Couldn't modify ChartRankingItemEntity because ChartRankingItem doesn't exist.");
        }
        $already = $chartRankingItemRepository->get($chartRankingItemEntity->businessId(), $chartRankingItemEntity->id());
        if (!empty($already)) {
            throw new ChartRankingItemException("Couldn't modify ChartRankingItemEntity because ChartRankingItem is already existing.");
        }
    }

    public function delete(EntityId $id, ChartRankingItemRepositoryInterface $chartRankingItemRepository)
    {
        $already = $chartRankingItemRepository->find($id);
        if (empty($already)) {
            throw new ChartRankingItemException("Couldn't delete ChartRankingItemEntity because ChartRankingItem doesn't exist.");
        }
    }

    public function buildQuery(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        EntityId $artistId = null,
        EntityId $musicId = null,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository
    ) {
        $builderContainer = $chartRankingItemRepository->builderWithChartArtist(null, $chartArtist);
        $builderContainer = $chartRankingItemRepository->builderWithChartMusic($builderContainer, $chartMusic);
        $builderContainer = $chartRankingItemRepository->builderWithArtistId($builderContainer, $artistId);
        $builderContainer = $chartRankingItemRepository->builderWithMusicId($builderContainer, $musicId);
        return $builderContainer->get($this->eloquentName);
    }

    public function notAttachedQuery(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository
    ) {
        $builderContainer = $chartRankingItemRepository->builderWithChartArtist(null, $chartArtist);
        $builderContainer = $chartRankingItemRepository->builderWithChartMusic($builderContainer, $chartMusic);
        $builderContainer = $chartRankingItemRepository->builderNotAttached($builderContainer);
        return $builderContainer->get($this->eloquentName);
    }

}
