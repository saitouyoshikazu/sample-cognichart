<?php

namespace App\Domain\Country;

interface CountryFactoryInterface
{

    public function create(string $idValue, string $countryName);

}

