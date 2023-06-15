<?php

namespace App\Domain\Chart;
use App\Domain\ValueObjects\Phase;

class ChartListSpecification
{

    function chartList(Phase $phase, ChartListRepositoryInterface $chartListRepository)
    {
        if ($phase->value() === Phase::provisioned) {
            return $chartListRepository->provisionedChartList();
        }
        return $chartListRepository->chartListWithCache($this);
    }

    function chartListWithCache(ChartListRepositoryInterface $chartListRepository)
    {
        $chartList = $chartListRepository->cachedChartList();
        if (!empty($chartList)) {
            return $chartList;
        }
        $chartList = $chartListRepository->releasedChartList();
        if (empty($chartList)) {
            return null;
        }
        $chartListRepository->storeCacheChartList($chartList);
        return $chartList;
    }

    function refreshCachedChartList(ChartListRepositoryInterface $chartListRepository)
    {
        $chartListRepository->deleteCachedChartList();
        $chartList = $chartListRepository->releasedChartList();
        if (empty($chartList)) {
            return;
        }
        $chartListRepository->storeCacheChartList($chartList);
    }

}
