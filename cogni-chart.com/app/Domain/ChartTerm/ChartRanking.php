<?php

namespace App\Domain\ChartTerm;
use App\Domain\EntityId;

class ChartRanking
{

    private $ranking;
    private $chartRankingItemId;

    public function __construct(int $ranking, EntityId $chartRankingItemId)
    {
        if ($ranking <= 0) {
            throw new ChartTermException("Ranking must be greater than 0. : {$ranking}");
        }
        $this->ranking = $ranking;
        $this->chartRankingItemId = $chartRankingItemId;
    }

    public function ranking()
    {
        return $this->ranking;
    }

    public function chartRankingItemId()
    {
        return $this->chartRankingItemId;
    }

}
