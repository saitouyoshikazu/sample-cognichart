<?php

namespace App\Domain\Country;

class CountryFactory implements CountryFactoryInterface
{

    public function create(string $idValue, string $countryName)
    {
        $countryId = new CountryId($idValue);
        $countryEntity = new CountryEntity($countryId);
        $countryEntity->setCountryName($countryName);
        return $countryEntity;
    }

}

