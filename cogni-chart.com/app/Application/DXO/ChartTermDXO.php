<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitPhase;
use App\Application\DXO\Traits\TraitEndDate;
use App\Application\DXO\Traits\TraitEntityId;
use App\Domain\ChartTerm\ChartTermBusinessId;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\EntityId;

class ChartTermDXO
{

    use TraitPhase, TraitEndDate, TraitEntityId;

    private $chartIdValue;
    private $startDateValue;
    private $rankings;
    private $publishReleasedMessageValue = false;

    public function list(string $chartIdValue, string $phaseValue)
    {
        $this->chartIdValue = $chartIdValue;
        $this->phaseValue = $phaseValue;
    }

    public function aggregation(string $chartIdValue, string $endDateValue)
    {
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
    }

    public function masterAggregation(string $phaseValue, string $chartIdValue, string $endDateValue)
    {
        $this->phaseValue = $phaseValue;
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
    }

    public function register(string $chartIdValue, string $startDateValue, string $endDateValue)
    {
        $this->chartIdValue = $chartIdValue;
        $this->startDateValue = $startDateValue;
        $this->endDateValue = $endDateValue;
    }

    public function release(string $entityIdValue, bool $publishReleasedMessageValue = false)
    {
        $this->entityIdValue = $entityIdValue;
        $this->publishReleasedMessageValue = $publishReleasedMessageValue;
    }

    public function delete(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function rollback(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function refreshCachedAggregation(string $entityIdValue, string $chartIdValue, string $endDateValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
    }

    public function resolve(string $phaseValue, string $entityIdValue)
    {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
    }

    public function getChartId()
    {
        $chartIdValue = trim($this->chartIdValue);
        if (empty($chartIdValue)) {
            return null;
        }
        return new EntityId($chartIdValue);
    }

    public function getStartDate()
    {
        $startDateValue = trim($this->startDateValue);
        if (empty($startDateValue)) {
            return null;
        }
        return new ChartTermDate($startDateValue);
    }

    public function getBusinessId()
    {
        $chartId = $this->getChartId();
        $endDate = $this->getEndDate();
        if (empty($chartId) || empty($endDate)) {
            return null;
        }
        return new ChartTermBusinessId($chartId, $endDate);
    }

    public function addRanking(int $ranking, string $chartRankingItemIdValue)
    {
        $row = new \StdClass();
        $row->ranking = $ranking;
        $row->chart_ranking_item_id = $chartRankingItemIdValue;
        $this->rankings[$ranking] = $row;
        return $this;
    }

    public function getChartRankings()
    {
        if (empty($this->rankings)) {
            return null;
        }
        return $this->rankings;
    }

    public function getPublishReleasedMessage()
    {
        return $this->publishReleasedMessageValue;
    }

}
