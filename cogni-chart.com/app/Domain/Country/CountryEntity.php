<?php

namespace App\Domain\Country;

class CountryEntity
{

    private $id;
    private $countryName;

    public function __construct(CountryId $id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function setCountryName(string $countryName)
    {
        $this->countryName = $countryName;
    }

    public function getCountryName()
    {
        return $this->countryName;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId()->value(),
            'countryName' => $this->getCountryName()
        ];
    }

}

