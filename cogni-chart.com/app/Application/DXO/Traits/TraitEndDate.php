<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\ChartTermDate;

trait TraitEndDate
{

    private $endDateValue;

    public function getEndDate()
    {
        $endDateValue = trim($this->endDateValue);
        if (empty($endDateValue)) {
            return null;
        }
        return new ChartTermDate($endDateValue);
    }

}
