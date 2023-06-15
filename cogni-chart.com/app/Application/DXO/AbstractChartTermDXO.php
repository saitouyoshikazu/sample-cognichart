<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitChartName;
use App\Application\DXO\Traits\TraitCountryId;
use App\Application\DXO\Traits\TraitChartId;
use App\Application\DXO\Traits\TraitEndDate;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\ChartTerm\ChartTermBusinessId;

class AbstractChartTermDXO
{

    use TraitChartName, TraitCountryId, TraitChartId, TraitEndDate;

    private $targetDateValue;
    private $dateIntervalValue;

    public function create(string $countryIdValue, string $chartNameValue, string $targetDateValue = null, string $dateIntervalValue = null)
    {
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
        $this->targetDateValue = $targetDateValue;
        $this->dateIntervalValue = $dateIntervalValue;
    }

    public function import(string $chartIdValue, string $endDateValue)
    {
        $this->chartIdValue = $chartIdValue;
        $this->endDateValue = $endDateValue;
    }

    public function getChartBusinessId()
    {
        $countryId = $this->getCountryId();
        $chartName = $this->getChartName();
        if (empty($countryId) || empty($chartName)) {
            return null;
        }
        return new ChartBusinessId($countryId, $chartName);
    }

    public function getTargetDate()
    {
        $targetDateValue = trim($this->targetDateValue);
        if (empty($targetDateValue)) {
            return null;
        }
        return new ChartTermDate($targetDateValue);
    }

    public function getInterval()
    {
        $dateIntervalValue = trim($this->dateIntervalValue);
        if (empty($dateIntervalValue)) {
            return null;
        }
        return new \DateInterval($dateIntervalValue);
    }

    public function getChartTermBusinessId()
    {
        $chartId = $this->getChartId();
        $endDate = $this->getEndDate();
        if (empty($chartId) || empty($endDate)) {
            return null;
        }
        return new ChartTermBusinessId($chartId, $endDate);
    }

}
