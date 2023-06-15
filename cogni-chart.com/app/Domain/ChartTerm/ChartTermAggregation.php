<?php

namespace App\Domain\ChartTerm;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartTermDate;

class ChartTermAggregation extends ChartTermEntity
{

    private $chartRankings;

    public function __construct(EntityId $id, EntityId $chartId, ChartTermDate $startDate, ChartTermDate $endDate)
    {
        parent::__construct($id, $chartId, $startDate, $endDate);
    }

    public function addChartRanking(ChartRanking $chartRanking)
    {
        $this->chartRankings[$chartRanking->ranking()] = $chartRanking;
        return $this;
    }

    public function removeChartRanking(ChartRanking $chartRanking)
    {
        $ranking = $chartRanking->ranking();
        if (empty($this->chartRankings[$ranking])) {
            return $this;
        }
        unset($this->chartRankings[$ranking]);
        return $this;
    }

    public function chartRankings()
    {
        return $this->chartRankings;
    }

}
