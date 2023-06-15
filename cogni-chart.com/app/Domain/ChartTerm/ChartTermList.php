<?php

namespace App\Domain\ChartTerm;
use App\Domain\ValueObjects\Phase;
use App\Domain\ValueObjects\ChartTermDate;

class ChartTermList implements \Iterator
{

    private $phase;
    private $position;
    private $chartTermEntities;

    public function __construct(Phase $phase)
    {
        $this->phase = $phase;
        $this->position = 0;
        $this->chartTermEntities = [];
    }

    public function append(ChartTermEntity $chartTermEntity)
    {
        $this->chartTermEntities[] = $chartTermEntity;
    }

    public function chartTermCount()
    {
        if (empty($this->chartTermEntities)) {
            return 0;
        }
        return count($this->chartTermEntities);
    }

    public function chartTermEntities()
    {
        return $this->chartTermEntities;
    }

    public function phase()
    {
        return $this->phase->value();
    }

    public function nearest(ChartTermDate $searchChartTermDate = null)
    {
        if (empty($this->chartTermEntities)) {
            return null;
        }
        $nearestChartTerm = $this->chartTermEntities[0];
        if (empty($searchChartTermDate)) {
            return $nearestChartTerm;
        }
        foreach ($this->chartTermEntities AS $chartTermEntity) {
            if ($chartTermEntity->endDate()->getDate() < $searchChartTermDate->getDate()) {
                return $nearestChartTerm;
            }
            $nearestChartTerm = $chartTermEntity;
        }
        return $nearestChartTerm;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->chartTermEntities[$this->position]);
    }

    public function current()
    {
        return $this->chartTermEntities[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

}
