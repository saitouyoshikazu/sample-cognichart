<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use App\Domain\Country\CountryId;
use App\Domain\Country\CountrySpecification;

class CountryRepositoryTest extends TestCase
{

    private $countryRepositoryInterfaceName = 'App\Domain\Country\CountryRepositoryInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testProvider()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);
        $this->assertEquals(get_class($countryRepository), 'App\Infrastructure\Repositories\CountryRepository');
    }

    public function testFind()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $idValue = 'ZZ';
        $countryId = new CountryId($idValue);
        $res = $countryRepository->find($countryId);
        $this->assertNull($res);

        $idValue = 'US';
        $countryId = new CountryId($idValue);
        $res = $countryRepository->find($countryId);
        $this->assertEquals($res->id()->value(), $idValue);
        $this->assertFalse($redisDAO->isCache());

        $countryRepository->find($countryId);
        $this->assertFalse($redisDAO->isCache());
    }

    public function testList()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);

        $countryIds = [new CountryId('ZZ')];
        $res = $countryRepository->list($countryIds);
        $this->assertNull($res);

        $countryIds = [new CountryId('US')];
        $res = $countryRepository->list($countryIds);
        $this->assertEquals(count($res), 1);
        $res = $res[0];
        $this->assertEquals($res->id()->value(), 'US');

        $countryIds = [new CountryId('US'), new CountryId('GB')];
        $res = $countryRepository->list($countryIds);
        $this->assertEquals(count($res), 2);
        foreach ($res AS $countryEntity) {
            $searchedIndex = array_search($countryEntity->id(), $countryIds);
            $searched = true;
            if ($searchedIndex === false) {
                $searched = false;
            }
            $this->assertTrue($searched);
        }

        $countryIds = [new CountryId('US'), new CountryId('GB'), new CountryId('ZZ')];
        $res = $countryRepository->list($countryIds);
        $this->assertEquals(count($res), 2);

        $countryIds = null;
        $res = $countryRepository->list($countryIds);
        $this->assertEquals(count($res), 130);
    }

    public function testCacheKey()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);

        $idValue = 'US';
        $countryId = new CountryId($idValue);
        $res = $countryRepository->cacheKey($countryId);
        $this->assertEquals($res, 'App\Domain\Country\CountryEntity:' . $idValue);
    }

    public function testStoreCache()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $exists = $redisDAO->keys('App\Domain\Country\CountryEntity:*');
        $this->assertEmpty($exists);

        $idValue = 'US';
        $countryId = new CountryId($idValue);
        $countryEntity = $countryRepository->find($countryId);
        $res = $countryRepository->storeCache($countryEntity);
        $this->assertTrue($res);
        $stored = $redisDAO->keys('App\Domain\Country\CountryEntity:*');
        $this->assertEquals($stored[0], $countryRepository->cacheKey($countryEntity->id()));
    }

    public function testFindCache()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $idValue = 'ZZ';
        $countryId = new CountryId($idValue);
        $cachedEntity = $countryRepository->findCache($countryId);
        $this->assertNull($cachedEntity);

        $idValue = 'US';
        $countryId = new CountryId($idValue);
        $countryEntity = $countryRepository->find($countryId);
        $countryRepository->storeCache($countryEntity);
        $this->assertFalse($redisDAO->isCache());
        $cachedEntity = $countryRepository->findCache($countryId);
        $this->assertEquals($cachedEntity, $countryEntity);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testFindWithCache()
    {
        $countryRepository = app($this->countryRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $countrySpecification = new CountrySpecification();

        $idValue = 'ZZ';
        $countryId = new CountryId($idValue);
        $countryEntity = $countryRepository->findWithCache($countryId, $countrySpecification);
        $this->assertNull($countryEntity);

        $idValue = 'US';
        $countryId = new CountryId($idValue);
        $countryEntity = $countryRepository->findWithCache($countryId, $countrySpecification);
        $this->assertEquals($countryEntity->id(), $countryId);
        $this->assertFalse($redisDAO->isCache());
        $countryRepository->findWithCache($countryId, $countrySpecification);
        $this->assertTrue($redisDAO->isCache());
    }

}
