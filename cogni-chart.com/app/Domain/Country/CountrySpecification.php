<?php

namespace App\Domain\Country;

class CountrySpecification
{

    public function findWithCache(CountryId $countryId, CountryRepositoryInterface $countryRepository)
    {
        $countryEntity = $countryRepository->findCache($countryId);
        if (!empty($countryEntity)) {
            return $countryEntity;
        }
        $countryEntity = $countryRepository->find($countryId);
        if (empty($countryEntity)) {
            return null;
        }
        $countryRepository->storeCache($countryEntity);
        return $countryEntity;
    }

}
