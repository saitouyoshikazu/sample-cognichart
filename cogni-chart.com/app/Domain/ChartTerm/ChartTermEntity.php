<?php

namespace App\Domain\ChartTerm;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\ValidationHandlerInterface;
use App\Domain\ValueObjects\ChartTermDate;

class ChartTermEntity extends Entity
{

    private $chartId;
    private $startDate;
    private $endDate;

    public function __construct(EntityId $id, EntityId $chartId, ChartTermDate $startDate, ChartTermDate $endDate)
    {
        parent::__construct($id);
        $this->setChartId($chartId)->setEndDate($endDate);
        $this->setStartDate($startDate);
    }

    protected function setBusinessId()
    {
        if (empty($this->chartId) || empty($this->endDate)) {
            return null;
        }
        if (empty($this->businessId)) {
            $this->businessId = new ChartTermBusinessId($this->chartId, $this->endDate);
        }
        $this->businessId->setChartId($this->chartId)->setEndDate($this->endDate);
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

    public function validate(ValidationHandlerInterface $handler)
    {
        $validator = new ChartTermValidator($this, $handler);
        $validator->validate();
    }

}
