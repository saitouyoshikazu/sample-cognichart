<?php

namespace App\Application\DXO\Traits;
use App\Domain\EntityId;

trait TraitChartId
{

    private $chartIdValue;

    public function getChartId()
    {
        $chartIdValue = trim($this->chartIdValue);
        if (empty($chartIdValue)) {
            return null;
        }
        return new EntityId($chartIdValue);
    }

}
