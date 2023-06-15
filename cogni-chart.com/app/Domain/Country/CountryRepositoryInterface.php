<?php

namespace App\Domain\Country;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Country\CountryFactoryInterface;
use App\Domain\Country\CountryId;
use App\Domain\Country\CountryEntity;
use App\Domain\Country\CountrySpecification;

interface CountryRepositoryInterface
{

    public function __construct(RedisDAOInterface $redisDAO, CountryFactoryInterface $countryFactory);

    public function find(CountryId $countryId);

    public function list(array $countryIds = null);

    public function cacheKey(CountryId $countryId);

    public function findCache(CountryId $countryId);

    public function storeCache(CountryEntity $countryEntity);

    public function findWithCache(CountryId $countryId, CountrySpecification $countrySpecification);

}

