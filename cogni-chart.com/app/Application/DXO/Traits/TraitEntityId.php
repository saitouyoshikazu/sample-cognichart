<?php

namespace App\Application\DXO\Traits;
use App\Domain\EntityId;

trait TraitEntityId
{

    private $entityIdValue;

    public function getEntityId()
    {
        $entityIdValue = trim($this->entityIdValue);
        if (empty($entityIdValue)) {
            return null;
        }
        return new EntityId($entityIdValue);
    }

}
