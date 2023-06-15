<?php

namespace App\Domain;

abstract class Entity
{

    protected $id;
    protected $businessId;

    public function __construct(EntityId $id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    abstract protected function setBusinessId();

    public function businessId()
    {
        return $this->businessId;
    }

    public function equals(Entity $comparative)
    {
        if (empty($comparative)) {
            return false;
        }
        if ($this->id()->equals($comparative->id())) {
            return true;
        }
        return false;
    }

    public function validate(ValidationHandlerInterface $handler)
    {
    }

}
