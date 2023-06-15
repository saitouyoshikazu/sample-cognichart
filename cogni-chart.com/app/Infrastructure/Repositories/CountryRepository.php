<?php

namespace App\Infrastructure\Repositories;
use App\Domain\Country\CountryRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Country\CountryFactoryInterface;
use App\Domain\Country\CountryId;
use App\Domain\Country\CountryEntity;
use App\Domain\Country\CountrySpecification;
use App\Infrastructure\Eloquents\Country;

class CountryRepository implements CountryRepositoryInterface
{

    private $redisDAO;

    private $countryFactory;

    public function __construct(RedisDAOInterface $redisDAO, CountryFactoryInterface $countryFactory)
    {
        $this->redisDAO = $redisDAO;
        $this->countryFactory = $countryFactory;
    }

    public function find(CountryId $countryId)
    {
        $row = Country::get($countryId->value());
        if (empty($row)) {
            return null;
        }
        $countryEntity = $this->countryFactory->create($row->id, $row->countryName);
        return $countryEntity;
    }

    public function list(array $countryIds = null)
    {
        $countryEntities = null;
        $countryIdValues = null;
        if (!empty($countryIds)) {
            foreach ($countryIds AS $countryId) {
                if ($countryId instanceof CountryId) {
                    $countryIdValues[] = $countryId->value();
                }
            }
        }
        $rows = Country::list($countryIdValues);
        if (empty($rows)) {
            return $countryEntities;
        }
        foreach ($rows AS $row) {
            $countryEntity = $this->countryFactory->create($row->id, $row->countryName);
            if (!empty($countryEntity)) {
                $countryEntities[] = $countryEntity;
            }
        }
        return $countryEntities;
    }

    public function cacheKey(CountryId $countryId)
    {
        return CountryEntity::class . ":" . $countryId->value();
    }

    public function findCache(CountryId $countryId)
    {
        $cache = $this->redisDAO->get($this->cacheKey($countryId));
        if (empty($cache)) {
            return null;
        }
        return unserialize($cache);
    }

    public function storeCache(CountryEntity $countryEntity)
    {
        return $this->redisDAO->set(
            $this->cacheKey($countryEntity->id()),
            serialize($countryEntity)
        );
    }

    public function findWithCache(CountryId $countryId, CountrySpecification $countrySpecification)
    {
        return $countrySpecification->findWithCache($countryId, $this);
    }

}

