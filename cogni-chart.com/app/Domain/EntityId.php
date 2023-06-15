<?php

namespace App\Domain;

class EntityId
{

    private $id;

    /**
     * Construct.
     * @param   string  $idValue    Value of id.
     * @throws  DomainLayerException
     */
    public function __construct(string $idValue)
    {
        $this->setId($idValue);
    }

    /**
     * Set value of id.
     * @param   string  $idValue    Value of id.
     * @throws  DomainLayerException
     */
    private function setId(string $idValue)
    {
        $idValue = trim($idValue);
        if (empty($idValue)) {
            throw new DomainLayerException("Can't set empty value in the id of entity.");
        }
        $this->id = $idValue;
    }

    /**
     * Get value of id.
     * @return  string  Value of id.
     */
    public function value()
    {
        return $this->id;
    }

    /**
     * Check if id is equals.
     * @param   EntityId    $comparative    Comparative EntityId.
     * @return  true    When id is equals.
     *          false   When id isn't equals.
     */
    public function equals(EntityId $comparative)
    {
        if ($this->value() === $comparative->value()) {
            return true;
        }
        return false;
    }

}
