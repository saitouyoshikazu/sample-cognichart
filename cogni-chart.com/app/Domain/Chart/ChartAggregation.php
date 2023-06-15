<?php

namespace App\Domain\Chart;
use App\Domain\EntityId;
use App\Domain\Country\CountryId;
use App\Domain\ValueObjects\ChartName;
use App\Domain\ChartTerm\ChartTermList;

class ChartAggregation extends ChartEntity
{

    private $chartTermList;

    public function __construct(EntityId $id, CountryId $countryId, ChartName $chartName)
    {
        parent::__construct($id, $countryId, $chartName);
    }

    public function setChartTermList(ChartTermList $chartTermList)
    {
        $this->chartTermList = $chartTermList;
        return $this;
    }

    public function chartTermList()
    {
        return $this->chartTermList;
    }

}
