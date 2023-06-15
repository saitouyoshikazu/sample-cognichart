<?php

namespace App\Domain\ValueObjects;

class SortColumn
{

    private $column;

    private $destination;

    public function __construct(string $column, string $destination)
    {
        $this->column = $column;
        $this->destination = $destination;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getDestination()
    {
        return $this->destination;
    }

}
