<?php

namespace App\Domain\ChartTerm;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartTermDate;

class ChartTermFactory implements ChartTermFactoryInterface
{

    public function create(
        string $idValue,
        string $chartIdValue,
        string $startDateValue,
        string $endDateValue
    ) {
        $id = new EntityId($idValue);
        $chartId = new EntityId($chartIdValue);
        $startDate = new ChartTermDate($startDateValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermEntity = new ChartTermEntity($id, $chartId, $startDate, $endDate);
        return $chartTermEntity;
    }

    public function toAggregation(ChartTermEntity $chartTermEntity)
    {
        $chartTermAggregation = new ChartTermAggregation(
            $chartTermEntity->id(),
            $chartTermEntity->chartId(),
            $chartTermEntity->startDate(),
            $chartTermEntity->endDate()
        );
        return $chartTermAggregation;
    }

    public function addChartRanking(
        ChartTermAggregation &$chartTermAggregation,
        int $ranking,
        string $chartRankingItemIdValue
    ) {
        $chartRankigItemId = new EntityId($chartRankingItemIdValue);
        $chartRanking = new ChartRanking($ranking, $chartRankigItemId);
        $chartTermAggregation->addChartRanking($chartRanking);
    }

}
