<?php

namespace App\Domain\ValueObjects;

class ApiName
{

    private $apiName;

    public function __construct(string $apiName)
    {
        $this->setApiName($apiName);
    }

    public function setApiName(string $apiName)
    {
        $apiName = trim($apiName);
        if (empty($apiName)) {
            throw new ValueObjectException("Can't set empty value in ApiName.");
        }
        $this->apiName = $apiName;
        return $this;
    }

    public function value()
    {
        return $this->apiName;
    }

}
