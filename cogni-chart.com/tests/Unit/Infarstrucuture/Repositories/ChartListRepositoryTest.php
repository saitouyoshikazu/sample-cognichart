<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\ValueObjects\Phase;
use App\Domain\Chart\ChartListSpecification;
use App\Domain\Chart\ChartList;

class ChartListRepositoryTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $chartListRepositoryInterfaceName = 'App\Domain\Chart\ChartListRepositoryInterface';
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
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $this->assertEquals(get_class($chartListRepository), 'App\Infrastructure\Repositories\ChartListRepository');
    }

    public function testReleasedChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $res = $chartListRepository->releasedChartList();
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        $verify = [
            '0a1b2c3d4e5f',
            'f5e4d3c2b1a0'
        ];

        $res = $chartListRepository->releasedChartList();
        $this->assertEquals($res->chartCount(), 2);
        $this->assertEquals($res->phase(), Phase::released);
        $result = [];
        foreach ($res AS $chartEntity) {
            $result[] = $chartEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
        $this->assertFalse($redisDAO->isCache());

        $redisDAO->clear('App\Domain\Country*');
        $chartListRepository->releasedChartList();
        $this->assertFalse($redisDAO->isCache());
    }

    public function testProvisionedChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        $res = $chartListRepository->provisionedChartList();
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        $verify = [
            'ff55ee44dd33cc22bb11aa00',
            '000aaa111bbb222ccc333ddd444eee55'
        ];

        $res = $chartListRepository->provisionedChartList();
        $this->assertEquals($res->chartCount(), 2);
        $this->assertEquals($res->phase(), Phase::provisioned);
        $result = [];
        foreach ($res AS $chartEntity) {
            $result[] = $chartEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
        $this->assertFalse($redisDAO->isCache());

        $redisDAO->clear('App\Domain\Country*');
        $chartListRepository->provisionedChartList();
        $this->assertFalse($redisDAO->isCache());
    }

    public function testStoreCacheChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $chartList = $chartListRepository->releasedChartList();
        $res = $chartListRepository->storeCacheChartList($chartList);
        $this->assertTrue($res);
    }

    public function testCachedChartList() {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $chartList = $chartListRepository->releasedChartList();
        $chartListRepository->storeCacheChartList($chartList);
        $redisDAO->clear('App\Domain\Country*');
        $res = $chartListRepository->cachedChartList();
        $this->assertEquals($res, $chartList);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testChartListWithCache()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartListSpecification = new ChartListSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $verify = [
            '0a1b2c3d4e5f',
            'f5e4d3c2b1a0'
        ];
        $res = $chartListRepository->chartListWithCache($chartListSpecification);
        $this->assertEquals($res->chartCount(), 2);
        $this->assertEquals($res->phase(), Phase::released);
        $result = [];
        foreach ($res AS $chartEntity) {
            $result[] = $chartEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country*');
        $chartListRepository->chartListWithCache($chartListSpecification);
        $this->assertTrue($redisDAO->isCache());
    }

    public function testChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartListSpecification = new ChartListSpecification();

        $phase = new Phase('released');
        $res = $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertNull($res);

        $phase = new Phase('provisioned');
        $res = $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $phase = new Phase('released');
        $verify = [
            '0a1b2c3d4e5f',
            'f5e4d3c2b1a0'
        ];
        $res = $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertEquals($res->phase(), Phase::released);
        $this->assertEquals($res->chartCount(), 2);
        $result = [];
        foreach ($res AS $chartEntity) {
            $result[] = $chartEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country*');
        $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertTrue($redisDAO->isCache());

        $phase = new Phase('provisioned');
        $verify = [
            'ff55ee44dd33cc22bb11aa00',
            '000aaa111bbb222ccc333ddd444eee55'
        ];
        $res = $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertEquals($res->chartCount(), 2);
        $this->assertEquals($res->phase(), Phase::provisioned);
        $result = [];
        foreach ($res AS $chartEntity) {
            $result[] = $chartEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->clear('App\Domain\Country*');
        $chartListRepository->chartList($phase, $chartListSpecification);
        $this->assertFalse($redisDAO->isCache());
    }

    public function testDeleteCachedChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $chartListSpecification = new ChartListSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $res = $chartListRepository->deleteCachedChartList();
        $this->assertEquals($res, 0);

        $chartList = $chartListRepository->chartListWithCache($chartListSpecification);
        $chartListRepository->storeCacheChartList($chartList);
        $res = $chartListRepository->deleteCachedChartList();
        $this->assertEquals($res, 1);
    }

    public function testRefreshCachedChartList()
    {
        $chartListRepository = app($this->chartListRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartListSpecification = new ChartListSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $redisDAO->set('App\Domain\Chart\ChartList', '1');
        $chartListRepository->refreshCachedChartList($chartListSpecification);
        $res = $chartListRepository->cachedChartList();
        $this->assertEquals(get_class($res), 'App\Domain\Chart\ChartList');
    }

}
