<?php

namespace App\Domain\AbstractChartTerm;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\EntityId;
use App\Domain\ChartTerm\ChartTermBusinessId;

class AbstractChartTerm
{

    private $chartId;
    private $startDate;
    private $endDate;
    private $businessId;
    private $rankings;

    public function __construct(
        EntityId $chartId,
        ChartTermDate $startDate,
        ChartTermDate $endDate
    ) {
        $this->setChartId($chartId)->setEndDate($endDate);
        $this->setStartDate($startDate);
    }

    private function setBusinessId()
    {
        if (empty($this->chartId) || empty($this->endDate)) {
            return null;
        }
        if (empty($this->businessId)) {
            $this->businessId = new ChartTermBusinessId($this->chartId, $this->endDate);
        }
        $this->businessId->setChartId($this->chartId)->setEndDate($this->endDate);
    }

    public function businessId()
    {
        return $this->businessId;
    }

    public function setChartId(EntityId $chartId)
    {
        $this->chartId = $chartId;
        $this->setBusinessId();
        return $this;
    }

    public function chartId()
    {
        return $this->chartId;
    }

    public function setStartDate(ChartTermDate $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function startDate()
    {
        return $this->startDate;
    }

    public function setEndDate(ChartTermDate $endDate)
    {
        $this->endDate = $endDate;
        $this->setBusinessId();
        return $this;
    }

    public function endDate()
    {
        return $this->endDate;
    }

    public function addRanking(int $ranking, string $chart_artist, string $chart_music)
    {
        $this->rankings[$ranking]['ranking'] = $ranking;
        $this->rankings[$ranking]['chart_artist'] = $chart_artist;
        $this->rankings[$ranking]['chart_music'] = $chart_music;
        return $this;
    }

    public function rankings()
    {
        return $this->rankings;
    }

}
