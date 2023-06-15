<?php

namespace App\Domain\Chart;
use App\Domain\BusinessIdInterface;
use App\Domain\Country\CountryId;
use App\Domain\ValueObjects\ChartName;

class ChartBusinessId implements BusinessIdInterface
{

    private $countryId;
    private $chartName;

    public function __construct(CountryId $countryId, ChartName $chartName)
    {
        $this->setCountryId($countryId)->setChartName($chartName);
    }

    public function setCountryId(CountryId $countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    public function countryId()
    {
        return $this->countryId;
    }

    public function setChartName(ChartName $chartName)
    {
        $this->chartName = $chartName;
        return $this;
    }

    public function chartName()
    {
        return $this->chartName;
    }

    public function value()
    {
        return $this->countryId()->value() . '-' . $this->chartName()->value();
    }

}
