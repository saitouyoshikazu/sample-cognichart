<?php

namespace App\Domain\ChartTerm;
use App\Domain\EntityId;
use App\Domain\ThrowException;

class ChartTermSpecification
{

    function getAggregationWithCache(ChartTermBusinessId $chartTermBusinessId, ChartTermRepositoryInterface $chartTermRepository)
    {
        $chartTermAggregation = $chartTermRepository->findCache($chartTermBusinessId, ChartTermAggregation::class);
        if (!empty($chartTermAggregation)) {
            return $chartTermAggregation;
        }
        $chartTermAggregation = $chartTermRepository->getAggregationRelease($chartTermBusinessId);
        if (empty($chartTermAggregation)) {
            return null;
        }
        $chartTermRepository->storeCache($chartTermAggregation, ChartTermAggregation::class);
        return $chartTermAggregation;
    }

    function refreshCachedAggregation(EntityId $entityId, ChartTermBusinessId $chartTermBusinessId, ChartTermRepositoryInterface $chartTermRepository)
    {
        $chartTermRepository->deleteCache($chartTermBusinessId, ChartTermAggregation::class);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        if (empty($chartTermAggregation)) {
            return;
        }
        $chartTermRepository->storeCache($chartTermAggregation, ChartTermAggregation::class);
    }

    function register(ChartTermAggregation $chartTermAggregation, ChartTermRepositoryInterface $chartTermRepository)
    {
        $releasedChartTermEntity = $chartTermRepository->findRelease($chartTermAggregation->id());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't register to provision ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->findProvision($chartTermAggregation->id());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't register to provision ChartTermEntity because provisioned ChartTerm is already existing.");
        }

        $releasedChartTermEntity = $chartTermRepository->getRelease($chartTermAggregation->businessId());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't register to provision ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->getProvision($chartTermAggregation->businessId());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't register to provision ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $chartTermAggregation->validate(new ThrowException(ChartTermException::class));
    }

    function modifyProvision(ChartTermAggregation $chartTermAggregation, ChartTermRepositoryInterface $chartTermRepository)
    {
        $releasedChartTermEntity = $chartTermRepository->findRelease($chartTermAggregation->id());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify provisioned ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->findProvision($chartTermAggregation->id());
        if (empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify provisioned ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }

        $releasedChartTermEntity = $chartTermRepository->getRelease($chartTermAggregation->businessId());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify provisioned ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->getProvision($chartTermAggregation->businessId(), $chartTermAggregation->id());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify provisioned ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $chartTermAggregation->validate(new ThrowException(ChartTermException::class));
    }

    function delete(EntityId $id, ChartTermRepositoryInterface $chartTermRepository)
    {
        $provisionedChartTermEntity = $chartTermRepository->findProvision($id);
        if (empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't delete provisioned ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }
    }

    function release(EntityId $id, ChartTermRepositoryInterface $chartTermRepository)
    {
        $releasedChartTermEntity = $chartTermRepository->findRelease($id);
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't release ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->findProvision($id);
        if (empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't release ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }

        $releaseTarget = $provisionedChartTermEntity;

        $releasedChartTermEntity = $chartTermRepository->getRelease($releaseTarget->businessId());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't release ChartTermEntity because released ChartTerm is already existing.");
        }
        $provisionedChartTermEntity = $chartTermRepository->getProvision($releaseTarget->businessId(), $releaseTarget->id());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't release ChartTermEntity because same provisioned ChartTerm is existing.");
        }
        return $releaseTarget;
    }

    function modifyRelease(ChartTermAggregation $chartTermAggregation, ChartTermRepositoryInterface $chartTermRepository)
    {
        $provisionedChartTermEntity = $chartTermRepository->findProvision($chartTermAggregation->id());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify released ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $releasedChartTermEntity = $chartTermRepository->findRelease($chartTermAggregation->id());
        if (empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify released ChartTermEntity because released ChartTerm doesn't exist.");
        }

        $provisionedChartTermEntity = $chartTermRepository->getProvision($chartTermAggregation->businessId());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify released ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $releasedChartTermEntity = $chartTermRepository->getRelease($chartTermAggregation->businessId(), $chartTermAggregation->id());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't modify released ChartTermEntity because released ChartTerm is already existing.");
        }
        $chartTermAggregation->validate(new ThrowException(ChartTermException::class));
    }

    function rollback(EntityId $id, ChartTermRepositoryInterface $chartTermRepository)
    {
        $provisionedChartTermEntity = $chartTermRepository->findProvision($id);
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't rollback ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $releasedChartTermEntity = $chartTermRepository->findRelease($id);
        if (empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't rollback ChartTermEntity because released ChartTerm doesn't exist.");
        }

        $rollbackTarget = $releasedChartTermEntity;

        $provisionedChartTermEntity = $chartTermRepository->getProvision($rollbackTarget->businessId());
        if (!empty($provisionedChartTermEntity)) {
            throw new ChartTermException("Couldn't rollback ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $releasedChartTermEntity = $chartTermRepository->getRelease($rollbackTarget->businessId(), $rollbackTarget->id());
        if (!empty($releasedChartTermEntity)) {
            throw new ChartTermException("Couldn't rollback ChartTermEntity because same released ChartTerm is existing.");
        }
        return $rollbackTarget;
    }

}
