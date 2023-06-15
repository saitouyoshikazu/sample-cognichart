<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\Phase;

trait TraitPhase
{

    private $phaseValue;

    public function getPhase()
    {
        $phaseValue = trim($this->phaseValue);
        if (empty($phaseValue)) {
            return null;
        }
        if ($phaseValue !== Phase::released && $phaseValue !== Phase::provisioned) {
            return null;
        }
        return new Phase($phaseValue);
    }

}
