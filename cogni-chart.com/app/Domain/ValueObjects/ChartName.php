<?php

namespace App\Domain\ValueObjects;

class ChartName
{

    private $chartName;

    public function __construct(string $chartName)
    {
        $chartName = trim($chartName);
        if (empty($chartName)) {
            throw new ValueObjectException("Can't set empty value in name of chart.");
        }
        $this->chartName = $chartName;
    }

    public function value()
    {
        return $this->chartName;
    }

}
