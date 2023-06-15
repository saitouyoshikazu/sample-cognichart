<?php

namespace App\Domain\ValueObjects;

class Phase
{

    const released = 'released';

    const provisioned = 'provisioned';

    private $phase;

    public function __construct(string $phase)
    {
        $phase = trim($phase);
        if (empty($phase)) {
            throw new ValueObjectException("Can't set empty value in Phase.");
        }
        if (!defined("self::{$phase}")) {
            throw new ValueObjectException("Invalid value of ValueObject. : " . self::class . "::{$phase} doesn't exist.");
        }
        $this->phase = $phase;
    }

    public function value()
    {
        return $this->phase;
    }

    public function isReleased()
    {
        if ($this->phase === self::released) {
            return true;
        }
        return false;
    }

    public function isProvisioned()
    {
        if ($this->phase === self::provisioned) {
            return true;
        }
        return false;
    }

}
