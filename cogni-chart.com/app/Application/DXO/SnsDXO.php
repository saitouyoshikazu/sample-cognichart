<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitCountryId;
use App\Application\DXO\Traits\TraitChartName;
use App\Application\DXO\Traits\TraitEndDate;
use App\Domain\Chart\ChartBusinessId;

class SnsDXO
{

    use TraitCountryId, TraitChartName, TraitEndDate;

    public function publishReleasedMessage(string $countryIdValue, string $chartNameValue, string $endDateValue)
    {
        $this->countryIdValue = $countryIdValue;
        $this->chartNameValue = $chartNameValue;
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

}
