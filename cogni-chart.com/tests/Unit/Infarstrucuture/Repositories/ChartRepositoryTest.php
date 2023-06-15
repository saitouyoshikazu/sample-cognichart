<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\ValueObjects\Phase;
use App\Domain\EntityId;
use App\Domain\Country\CountryId;
use App\Domain\ValueObjects\ChartName;
use App\Domain\Chart\ChartSpecification;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\Chart\ChartException;
use App\Infrastructure\Eloquents\ProvisionedChart;
use App\Infrastructure\Eloquents\Chart;

class ChartRepositoryTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $chartRepositoryInterfaceName = 'App\Domain\Chart\ChartRepositoryInterface';
    private $chartFactoryInterfaceName = 'App\Domain\Chart\ChartFactoryInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testProvider()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $this->assertEquals(get_class($chartRepository), 'App\Infrastructure\Repositories\ChartRepository');
    }

    public function testCreateId()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $res = $chartRepository->createId();
        $this->assertEquals(strlen($res->value()), 32);
    }

    public function testFindProvision()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findProvision($entityId);
        $this->assertNull($res);

        $idValue = '000aaa111bbb222ccc333ddd444eee';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findProvision($entityId);
        $this->assertNull($res);

        $idValue = 'ff55ee44dd33cc22bb11aa00';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findProvision($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
    }

    public function testFindRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findRelease($entityId);
        $this->assertNull($res);

        $idValue = '00aa11bb22cc33dd44ee55ff';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findRelease($entityId);
        $this->assertNull($res);

        $idValue = '0a1b2c3d4e5f';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->findRelease($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
    }

    public function testGetProvision()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'AU';
        $chartNameValue = 'DoesNotExist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getProvision($chartBusinessId);
        $this->assertNull($res);

        $countryIdValue = 'ZZ';
        $chartNameValue = 'Country Does Not Exist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getProvision($chartBusinessId);
        $this->assertNull($res);

        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getProvision($chartBusinessId);
        $this->assertEquals($res->countryId(), $countryId);
        $this->assertEquals($res->chartName(), $chartName);
    }

    public function testGetRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'DoesNotExist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getRelease($chartBusinessId);
        $this->assertNull($res);

        $countryIdValue = 'ZZ';
        $chartNameValue = 'Country Does Not Exist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getRelease($chartBusinessId);
        $this->assertNull($res);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $res = $chartRepository->getRelease($chartBusinessId);
        $this->assertEquals($res->countryId(), $countryId);
        $this->assertEquals($res->chartName(), $chartName);
    }

    public function testFindAggregationProvision()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $idValue = '00000000000000000000000000000000';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $idValue = '0a1b2c3d4e5f';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $idValue = 'ff55ee44dd33cc22bb11aa00';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertEquals($chartAggregation->id()->value(), $idValue);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '2023456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '2013456789abcdef0123456789abcdef');

        $idValue = 'ff55ee44dd33cc22bb11aa00';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationProvision($id, $chartTermPhase);
        $this->assertEquals($chartAggregation->id()->value(), $idValue);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '2123456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '2113456789abcdef0123456789abcdef');
    }

    public function testFindAggregationRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $idValue = '00000000000000000000000000000000';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $idValue = 'ff55ee44dd33cc22bb11aa00';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $idValue = '0a1b2c3d4e5f';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertEquals($chartAggregation->id()->value(), $idValue);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '0023456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '0013456789abcdef0123456789abcdef');

        $idValue = '0a1b2c3d4e5f';
        $id = new EntityId($idValue);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->findAggregationRelease($id, $chartTermPhase);
        $this->assertEquals($chartAggregation->id()->value(), $idValue);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '0123456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '0113456789abcdef0123456789abcdef');
    }

    public function testGetAggregationProvision()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $countryIdValue = 'AU';
        $chartNameValue = 'DoesNotExist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertEquals($chartAggregation->countryId(), $countryId);
        $this->assertEquals($chartAggregation->chartName(), $chartName);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '2023456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '2013456789abcdef0123456789abcdef');

        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationProvision($chartBusinessId, $chartTermPhase);
        $this->assertEquals($chartAggregation->countryId(), $countryId);
        $this->assertEquals($chartAggregation->chartName(), $chartName);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '2123456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '2113456789abcdef0123456789abcdef');
    }

    public function testGetAggregationRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'DoesNotExist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertNull($chartAggregation);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::provisioned);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertEquals($chartAggregation->countryId(), $countryId);
        $this->assertEquals($chartAggregation->chartName(), $chartName);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '0023456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '0013456789abcdef0123456789abcdef');

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartTermPhase = new Phase(Phase::released);
        $chartAggregation = $chartRepository->getAggregationRelease($chartBusinessId, $chartTermPhase);
        $this->assertEquals($chartAggregation->countryId(), $countryId);
        $this->assertEquals($chartAggregation->chartName(), $chartName);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '0123456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '0113456789abcdef0123456789abcdef');
    }

    public function testGetAggregationWithCache()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'DoesNotExist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartAggregation = $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertNull($chartAggregation);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country\CountryEntity:*');
        $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertFalse($redisDAO->isCache());

        $redisDAO->resetIsCache();

        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartAggregation = $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertNull($chartAggregation);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country\CountryEntity:*');
        $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertFalse($redisDAO->isCache());

        $redisDAO->resetIsCache();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartAggregation = $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertEquals($chartAggregation->countryId(), $countryId);
        $this->assertEquals($chartAggregation->chartName(), $chartName);
        $chartTermList = $chartAggregation->chartTermList();
        $this->assertEquals($chartTermList->chartTermCount(), 2);
        $chartTermEntities = $chartTermList->chartTermEntities();
        $chartTermEntity = $chartTermEntities[0];
        $this->assertEquals($chartTermEntity->id()->value(), '0123456789abcdef0123456789abcdef');
        $chartTermEntity = $chartTermEntities[1];
        $this->assertEquals($chartTermEntity->id()->value(), '0113456789abcdef0123456789abcdef');
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country\CountryEntity:*');
        $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testRefreshCachedAggregation()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $redisDAO->set('App\Domain\Chart\ChartAggregation:US-Billboard Hot 100', '1');

        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $entityId = new EntityId($entityIdValue);
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $chartRepository->refreshCachedAggregation($entityId, $chartBusinessId, $chartSpecification);
        $redisDAO->clear('App\Domain\Country\CountryEntity:*');
        $chartAggregation = $chartRepository->getAggregationWithCache($chartBusinessId, $chartSpecification);
        $this->assertEquals(get_class($chartAggregation), 'App\Domain\Chart\ChartAggregation');
        $this->assertTrue($redisDAO->isCache());
    }

    public function testRegister()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartFactory = app($this->chartFactoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $entityId = new EntityId('0a1b2c3d4e5f');
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $scheme = 'https';
        $host = 'www.billboard.com';
        $uri = 'charts/hot-200';
        $chartEntity = $chartFactory->create(
            $entityId->value(),
            $countryIdValue,
            $chartNameValue,
            $scheme,
            $host,
            $uri
        );
        $exception = false;
        try {
            $chartRepository->register($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityId = new EntityId('ff55ee44dd33cc22bb11aa00');
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $scheme = 'https';
        $host = 'www.billboard.com';
        $uri = 'charts/hot-200';
        $chartEntity = $chartFactory->create(
            $entityId->value(),
            $countryIdValue,
            $chartNameValue,
            $scheme,
            $host,
            $uri
        );
        $exception = false;
        try {
            $chartRepository->register($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityId = $chartRepository->createId();
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $scheme = 'https';
        $host = 'www.billboard.com';
        $uri = 'charts/hot-100';
        $chartEntity = $chartFactory->create(
            $entityId->value(),
            $countryIdValue,
            $chartNameValue,
            $scheme,
            $host,
            $uri
        );
        $exception = false;
        try {
            $chartRepository->register($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityId = $chartRepository->createId();
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $scheme = 'https';
        $host = 'www.ariacharts.com.au';
        $uri = 'charts/singles-chart';
        $chartEntity = $chartFactory->create(
            $entityId->value(),
            $countryIdValue,
            $chartNameValue,
            $scheme,
            $host,
            $uri
        );
        $exception = false;
        try {
            $chartRepository->register($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);
 
        $entityId = $chartRepository->createId();
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $scheme = 'https';
        $host = 'www.billboard.com';
        $uri = 'charts/hot-200';
        $chartEntity = $chartFactory->create(
            $entityId->value(),
            $countryIdValue,
            $chartNameValue,
            $scheme,
            $host,
            $uri
        );
        $res = $chartRepository->register($chartEntity, $chartSpecification);
        $this->assertTrue($res);
    }

    public function testModiyProvision()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartFactory = app($this->chartFactoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $entityIdValue = '0a1b2c3d4e5f';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findRelease($entityId);
        $exception = false;
        try {
            $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $chartEntity = $chartFactory->create(
            '00000000000000000000000000000000',
            'AU',
            'ARIA SINGLES CHART',
            'https',
            'www.ariacharts.com.au',
            'charts/singles-chart'
        );
        $exception = false;
        try {
            $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartEntity because provisioned chart doesn't exist.");
        }
        $this->assertTrue($exception);

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findProvision($entityId);
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $exception = false;
        try {
            $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = '000aaa111bbb222ccc333ddd444eee55';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findProvision($entityId);
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $exception = false;
        try {
            $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = '000aaa111bbb222ccc333ddd444eee55';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findProvision($entityId);
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $res = $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        $this->assertTrue($res);

        $entityIdValue = '000aaa111bbb222ccc333ddd444eee55';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findProvision($entityId);
        $chartEntity->setHost('www.ariacharts.modify.com.au');
        $res = $chartRepository->modifyProvision($chartEntity, $chartSpecification);
        $this->assertTrue($res);
    }

    public function testDelete()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartRepository->delete($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't delete provisioned ChartEntity because provisioned chart doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '000aaa111bbb222ccc333ddd444eee55';
        $entityId = new EntityId($idValue);
        $res = $chartRepository->delete($entityId, $chartSpecification);
        $this->assertTrue($res);
    }

    public function testRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $entityIdValue = '0a1b2c3d4e5f';
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->release($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = '00000000000000000000000000000000';
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->release($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartEntity because provisioned chart doesn't exist.");
        }
        $this->assertTrue($exception);

        $before = [
            'id'            =>  '000aaa111bbb222ccc333ddd444eee55',
            'country_id'    =>  'GB',
            'chart_name'    =>  'Official Singles Chart Top 200'
        ];

        ProvisionedChart::where(['id' => $before['id']])->update([
            'country_id'    =>  'US',
            'chart_name'    =>  'Billboard Hot 100'
        ]);
        $entityIdValue = $before['id'];
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->release($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        ProvisionedChart::where(['id' => $before['id']])->update([
            'country_id'    =>  'AU',
            'chart_name'    =>  'ARIA SINGLES CHART'
        ]);
        $entityIdValue = $before['id'];
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->release($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartEntity because same provisioned chart is existing.");
        }
        $this->assertTrue($exception);

        ProvisionedChart::where(['id' => $before['id']])->update([
            'country_id'    =>  $before['country_id'],
            'chart_name'    =>  $before['chart_name']
        ]);

        $entityIdValue = '000aaa111bbb222ccc333ddd444eee55';
        $entityId = new EntityId($entityIdValue);
        $res = $chartRepository->release($entityId, $chartSpecification);
        $this->assertTrue($res);
    }

    public function testModiyRelease()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartFactory = app($this->chartFactoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findProvision($entityId);
        $exception = false;
        try {
            $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        $chartEntity = $chartFactory->create(
            '00000000000000000000000000000000',
            'US',
            'Billboard Hot 100',
            'scheme',
            'www.billboard.com',
            'charts/hot-100'
        );
        $exception = false;
        try {
            $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartEntity because released chart doesn't exist.");
        }
        $this->assertTrue($exception);

        $entityIdValue = 'f5e4d3c2b1a0';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findRelease($entityId);
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $exception = false;
        try {
            $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = 'f5e4d3c2b1a0';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findRelease($entityId);
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $exception = false;
        try {
            $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartEntity because released chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = 'f5e4d3c2b1a0';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findRelease($entityId);
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartEntity
            ->setCountryId($countryId)
            ->setChartName($chartName);
        $res = $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        $this->assertTrue($res);

        $entityIdValue = 'f5e4d3c2b1a0';
        $entityId = new EntityId($entityIdValue);
        $chartEntity = $chartRepository->findRelease($entityId);
        $chartEntity
            ->setHost('www.billboard.com')
            ->setUri('charts/hot-200');
        $res = $chartRepository->modifyRelease($chartEntity, $chartSpecification);
        $this->assertTrue($res);
    }

    public function testRollback()
    {
        $chartRepository = app($this->chartRepositoryInterfaceName);
        $chartSpecification = new ChartSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->rollback($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        $entityIdValue = '00000000000000000000000000000000';
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->rollback($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartEntity because released chart doesn't exist.");
        }
        $this->assertTrue($exception);

        $before = [
            'id'            =>  'f5e4d3c2b1a0',
            'country_id'    =>  'GB',
            'chart_name'    =>  'Official Singles Chart Top 100'
        ];

        Chart::where(['id' => $before['id']])->update([
            'country_id'    =>  'AU',
            'chart_name'    =>  'ARIA SINGLES CHART'
        ]);
        $entityIdValue = $before['id'];
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->rollback($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartEntity because provisioned chart is already existing.");
        }
        $this->assertTrue($exception);

        Chart::where(['id' => $before['id']])->update([
            'country_id'    =>  'US',
            'chart_name'    =>  'Billboard Hot 100'
        ]);
        $entityIdValue = $before['id'];
        $entityId = new EntityId($entityIdValue);
        $exception = false;
        try {
            $chartRepository->rollback($entityId, $chartSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartEntity because same released chart is existing.");
        }
        $this->assertTrue($exception);

        Chart::where(['id' => $before['id']])->update([
            'country_id'    =>  $before['country_id'],
            'chart_name'    =>  $before['chart_name']
        ]);

        $entityIdValue = 'f5e4d3c2b1a0';
        $entityId = new EntityId($entityIdValue);
        $res = $chartRepository->rollback($entityId, $chartSpecification);
        $this->assertTrue($res);
    }

}
