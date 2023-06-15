<?php

namespace App\Application\DXO\Traits;
use App\Domain\Country\CountryId;

trait TraitCountryId
{

    private $countryIdValue;

    public function getCountryId()
    {
        $countryIdValue = trim($this->countryIdValue);
        if (empty($countryIdValue)) {
            return null;
        }
        return new CountryId($countryIdValue);
    }

}
