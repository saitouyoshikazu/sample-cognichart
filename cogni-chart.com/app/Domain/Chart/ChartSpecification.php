<?php

namespace App\Domain\Chart;
use App\Domain\EntityId;
use App\Domain\ValueObjects\Phase;

class ChartSpecification
{

    function getAggregationWithCache(ChartBusinessId $chartBusinessId, ChartRepositoryInterface $chartRepository)
    {
        $cachedAggregation = $chartRepository->findCache($chartBusinessId, ChartAggregation::class);
        if (!empty($cachedAggregation)) {
            return $cachedAggregation;
        }
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, new Phase(Phase::released));
        if (empty($chartAggregation)) {
            return null;
        }
        $chartRepository->storeCache($chartAggregation, ChartAggregation::class);
        return $chartAggregation;
    }

    function refreshCachedAggregation(EntityId$entityId, ChartBusinessId $businessId, ChartRepositoryInterface $chartRepository)
    {
        $chartRepository->deleteCache($businessId, ChartAggregation::class);
        $chartAggregation = $chartRepository->findAggregationRelease($entityId, new Phase(Phase::released));
        if (empty($chartAggregation)) {
            return;
        }
        $chartRepository->storeCache($chartAggregation, ChartAggregation::class);
    }

    function register(ChartEntity $chartEntity, ChartRepositoryInterface $chartRepository)
    {
        $releasedChartEntity = $chartRepository->findRelease($chartEntity->id());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't register to provision ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->findProvision($chartEntity->id());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't register to provision ChartEntity because provisioned chart is already existing.");
        }

        $releasedChartEntity = $chartRepository->getRelease($chartEntity->businessId());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't register to provision ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->getProvision($chartEntity->businessId());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't register to provision ChartEntity because provisioned chart is already existing.");
        }
    }

    function modifyProvision(ChartEntity $chartEntity, ChartRepositoryInterface $chartRepository)
    {
        $releasedChartEntity = $chartRepository->findRelease($chartEntity->id());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't modify provisioned ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->findProvision($chartEntity->id());
        if (empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't modify provisioned ChartEntity because provisioned chart doesn't exist.");
        }

        $releasedChartEntity = $chartRepository->getRelease($chartEntity->businessId());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't modify provisioned ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->getProvision($chartEntity->businessId(), $chartEntity->id());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't modify provisioned ChartEntity because provisioned chart is already existing.");
        }
    }

    function delete(EntityId $id, ChartRepositoryInterface $chartRepository)
    {
        $provisionedChartEntity = $chartRepository->findProvision($id);
        if (empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't delete provisioned ChartEntity because provisioned chart doesn't exist.");
        }
    }

    function release(EntityId $id, ChartRepositoryInterface $chartRepository)
    {
        $releasedChartEntity = $chartRepository->findRelease($id);
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't release ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->findProvision($id);
        if (empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't release ChartEntity because provisioned chart doesn't exist.");
        }

        $releaseTarget = $provisionedChartEntity;

        $releasedChartEntity = $chartRepository->getRelease($releaseTarget->businessId());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't release ChartEntity because released chart is already existing.");
        }
        $provisionedChartEntity = $chartRepository->getProvision($releaseTarget->businessId(), $releaseTarget->id());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't release ChartEntity because same provisioned chart is existing.");
        }
        return $releaseTarget;
    }

    function modifyRelease(ChartEntity $chartEntity, ChartRepositoryInterface $chartRepository)
    {
        $provisionedChartEntity = $chartRepository->findProvision($chartEntity->id());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't modify released ChartEntity because provisioned chart is already existing.");
        }
        $releasedChartEntity = $chartRepository->findRelease($chartEntity->id());
        if (empty($releasedChartEntity)) {
            throw new ChartException("Couldn't modify released ChartEntity because released chart doesn't exist.");
        }

        $provisionedChartEntity = $chartRepository->getProvision($chartEntity->businessId());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't modify released ChartEntity because provisioned chart is already existing.");
        }
        $releasedChartEntity = $chartRepository->getRelease($chartEntity->businessId(), $chartEntity->id());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't modify released ChartEntity because released chart is already existing.");
        }
    }

    function rollback(EntityId $id, ChartRepositoryInterface $chartRepository)
    {
        $provisionedChartEntity = $chartRepository->findProvision($id);
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't rollback ChartEntity because provisioned chart is already existing.");
        }
        $releasedChartEntity = $chartRepository->findRelease($id);
        if (empty($releasedChartEntity)) {
            throw new ChartException("Couldn't rollback ChartEntity because released chart doesn't exist.");
        }

        $rollbackTarget = $releasedChartEntity;

        $provisionedChartEntity = $chartRepository->getProvision($rollbackTarget->businessId());
        if (!empty($provisionedChartEntity)) {
            throw new ChartException("Couldn't rollback ChartEntity because provisioned chart is already existing.");
        }
        $releasedChartEntity = $chartRepository->getRelease($rollbackTarget->businessId(), $rollbackTarget->id());
        if (!empty($releasedChartEntity)) {
            throw new ChartException("Couldn't rollback ChartEntity because same released chart is existing.");
        }
        return $rollbackTarget;
    }

}
