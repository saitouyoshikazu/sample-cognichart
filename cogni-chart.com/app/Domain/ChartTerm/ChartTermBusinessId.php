<?php

namespace App\Domain\ChartTerm;
use App\Domain\BusinessIdInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartTermDate;

class ChartTermBusinessId implements BusinessIdInterface
{

    private $chartId;
    private $endDate;

    public function __construct(EntityId $chartId, ChartTermDate $endDate)
    {
        $this->setChartId($chartId)->setEndDate($endDate);
    }

    public function setChartId(EntityId $chartId)
    {
        $this->chartId = $chartId;
        return $this;
    }

    public function chartId()
    {
        return $this->chartId;
    }

    public function setEndDate(ChartTermDate $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function endDate()
    {
        return $this->endDate;
    }

    public function value()
    {
        return $this->chartId()->value() . '-' . $this->endDate()->value();
    }

}
