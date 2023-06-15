<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\ChartName;

trait TraitChartName
{

    private $chartNameValue;

    public function getChartName()
    {
        $chartNameValue = trim($this->chartNameValue);
        if (empty($chartNameValue)) {
            return null;
        }
        return new ChartName($chartNameValue);
    }

}
