<?php

namespace App\Domain\AdminUser;

class AdminUserId
{

    private $id;

    public function __construct(int $idValue)
    {
        $this->id = $idValue;
    }

    public function value()
    {
        return $this->id;
    }

    public function equals(AdminUserId $comparative)
    {
        if ($comparative->value() === $this->id)
        {
            return true;
        }
        return false;
    }

}
