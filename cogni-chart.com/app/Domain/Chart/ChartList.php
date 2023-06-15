<?php

namespace App\Domain\Chart;
use App\Domain\ValueObjects\Phase;

class ChartList implements \Iterator
{

    private $phase;
    private $position;
    private $chartEntities;

    public function __construct(Phase $phase)
    {
        $this->phase = $phase;
        $this->position = 0;
        $this->chartEntities = [];
    }

    public function append(ChartEntity $chartEntity)
    {
        $this->chartEntities[] = $chartEntity;
    }

    public function chartCount()
    {
        if (empty($this->chartEntities)) {
            return 0;
        }
        return count($this->chartEntities);
    }

    public function chartEntities()
    {
        return $this->chartEntities;
    }

    public function phase()
    {
        return $this->phase->value();
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return isset($this->chartEntities[$this->position]);
    }

    public function current()
    {
        return $this->chartEntities[$this->position];
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
