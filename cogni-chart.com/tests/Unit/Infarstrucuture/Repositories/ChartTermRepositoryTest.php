<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\ChartTerm\ChartTermAggregation;
use App\Domain\ChartTerm\ChartTermSpecification;
use App\Domain\ChartTerm\ChartTermBusinessId;
use App\Domain\ChartTerm\ChartTermException;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;
use App\Infrastructure\Eloquents\ChartTerm;

class ChartTermRepositoryTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $chartTermRepositoryInterfaceName = 'App\Domain\ChartTerm\ChartTermRepositoryInterface';
    private $chartTermFactoryInterfaceName = 'App\Domain\ChartTerm\ChartTermFactoryInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();
    }

    public function testProvider()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $this->assertEquals(get_class($chartTermRepository), 'App\Infrastructure\Repositories\ChartTermRepository');
    }

    public function testCreateId()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        $res = $chartTermRepository->createId();
        $this->assertEquals(strlen($res->value()), 32);
    }

    public function testFindProvision()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findProvision($entityId);
        $this->assertNull($res);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findProvision($entityId);
        $this->assertNull($res);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findProvision($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
    }

    public function testFindRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findRelease($entityId);
        $this->assertNull($res);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findRelease($entityId);
        $this->assertNull($res);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findRelease($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
    }

    public function testGetProvision()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $endDateValue = '2017-12-02';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getProvision($chartTermBusinessId);
        $this->assertNull($res);

        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getProvision($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);

        $excludeId = $res->id();
        $res = $chartTermRepository->getProvision($chartTermBusinessId, $excludeId);
        $this->assertNull($res);
    }

    public function testGetRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getRelease($chartTermBusinessId);
        $this->assertNull($res);

        $endDateValue = '2017-12-02';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getRelease($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);

        $excludeId = $res->id();
        $res = $chartTermRepository->getRelease($chartTermBusinessId, $excludeId);
        $this->assertNull($res);
    }

    public function testFindAggregationProvision()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationProvision($entityId);
        $this->assertNull($res);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationProvision($entityId);
        $this->assertNull($res);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationProvision($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
        $chartRankings = $res->chartRankings();
        $this->assertEquals(count($chartRankings), 8);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationProvision($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
        $chartRankings = $res->chartRankings();
        $this->assertNull($chartRankings);
    }

    public function testFindAggregationRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationRelease($entityId);
        $this->assertNull($res);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationRelease($entityId);
        $this->assertNull($res);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationRelease($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
        $chartRankings = $res->chartRankings();
        $this->assertEquals(count($chartRankings), 8);

        $idValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->findAggregationRelease($entityId);
        $this->assertEquals($res->id()->value(), $idValue);
        $chartRankings = $res->chartRankings();
        $this->assertNull($chartRankings);
    }

    public function testGetAggregationProvision()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $endDateValue = '2017-12-02';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationProvision($chartTermBusinessId);
        $this->assertNull($res);

        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationProvision($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertEquals(count($chartRankings), 8);

        $endDateValue = '2017-12-23';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationProvision($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertNull($chartRankings);
    }

    public function testGetAggregationRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationRelease($chartTermBusinessId);
        $this->assertNull($res);

        $endDateValue = '2017-12-02';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationRelease($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertEquals(count($chartRankings), 8);

        $endDateValue = '2017-12-09';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationRelease($chartTermBusinessId);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertNull($chartRankings);
    }

    public function testGetAggregationWithCache()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertNull($res);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->resetIsCache();
        $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->resetIsCache();

        $endDateValue = '2017-12-02';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertEquals(count($chartRankings), 8);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->resetIsCache();
        $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertTrue($redisDAO->isCache());
        $redisDAO->resetIsCache();

        $endDateValue = '2017-12-09';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);
        $res = $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertEquals($res->chartId(), $chartId);
        $this->assertEquals($res->endDate(), $endDate);
        $chartRankings = $res->chartRankings();
        $this->assertNull($chartRankings);
        $this->assertFalse($redisDAO->isCache());
        $redisDAO->resetIsCache();
        $chartTermRepository->getAggregationWithCache($chartTermBusinessId, $chartTermSpecification);
        $this->assertTrue($redisDAO->isCache());
        $redisDAO->resetIsCache();
    }

    public function testRefreshCachedAggregation()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $entityId = new EntityId($entityIdValue);
        $chartId = new EntityId($chartIdValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermBusinessId = new ChartTermBusinessId($chartId, $endDate);

        $cacheKey = ChartTermAggregation::class . ':' . $chartTermBusinessId->value();
        $redisDAO->set($cacheKey, serialize('1'));

        $chartTermRepository->refreshCachedAggregation($entityId, $chartTermBusinessId, $chartTermSpecification);
        $cached = $chartTermRepository->findCache($chartTermBusinessId, ChartTermAggregation::class);
        $this->assertEquals(get_class($cached), ChartTermAggregation::class);
    }

    public function testRegister()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermFactory = app($this->chartTermFactoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';

        $idValue = '0113456789abcdef0123456789abcdef';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0013456789abcdef0123456789abcdef';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0033456789abcdef0123456789abcdef';
        $startDateValue = '2017-11-26';
        $endDateValue = '2017-12-02';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0033456789abcdef0123456789abcdef';
        $startDateValue = '2017-12-10';
        $endDateValue = '2017-12-16';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't register to provision ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0033456789abcdef0123456789abcdef';
        $startDateValue = '2017-12-30';
        $endDateValue = '2017-12-24';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Start date of ChartTerm must be before than end date.");
        }
        $this->assertTrue($exception);

        $idValue = '0033456789abcdef0123456789abcdef';
        $startDateValue = '2017-12-24';
        $endDateValue = '2017-12-30';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $res = $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);
        $aggre = $chartTermRepository->findAggregationProvision(new EntityId($idValue));
        $chartRankings = $aggre->chartRankings();
        $this->assertNull($chartRankings);

        $idValue = '0043456789abcdef0123456789abcdef';
        $startDateValue = '2017-12-31';
        $endDateValue = '2018-01-06';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $rankingsRows = [
            [
                'ranking'               =>  1,
                'chart_ranking_item_id' =>  '0123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  2,
                'chart_ranking_item_id' =>  '1123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  3,
                'chart_ranking_item_id' =>  '2123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  4,
                'chart_ranking_item_id' =>  '3123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  5,
                'chart_ranking_item_id' =>  '4123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  6,
                'chart_ranking_item_id' =>  '5123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  7,
                'chart_ranking_item_id' =>  '6123456789abcdef0123456789abcdef'
            ],
            [
                'ranking'               =>  8,
                'chart_ranking_item_id' =>  '7123456789abcdef0123456789abcdef'
            ]
        ];
        foreach ($rankingsRows AS $rankingRow) {
            $chartTermFactory->addChartRanking(
                $chartTermAggregation,
                $rankingRow['ranking'],
                $rankingRow['chart_ranking_item_id']
            );
        }
        $res = $chartTermRepository->register($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);
        $aggre = $chartTermRepository->findAggregationProvision(new EntityId($idValue));
        $chartRankings = $aggre->chartRankings();
        $this->assertEquals(count($chartRankings), 8);
    }

    public function testModifyProvision()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermFactory = app($this->chartTermFactoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $chartIdValue = '0a1b2c3d4e5f';

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $exception = false;
        try {
            $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '00000000000000000000000000000000';
        $startDateValue = '2017-12-24';
        $endDateValue = '2017-12-30';
        $chartTermEntity = $chartTermFactory->create(
            $idValue,
            $chartIdValue,
            $startDateValue,
            $endDateValue
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $startDateValue = '2017-11-26';
        $endDateValue = '2017-12-02';
        $startDate = new ChartTermDate($startDateValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation
            ->setStartDate($startDate)
            ->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $startDateValue = '2017-12-10';
        $endDateValue = '2017-12-16';
        $startDate = new ChartTermDate($startDateValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation
            ->setStartDate($startDate)
            ->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify provisioned ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $startDateValue = '2017-12-30';
        $endDateValue = '2017-12-24';
        $startDate = new ChartTermDate($startDateValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation
            ->setStartDate($startDate)
            ->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Start date of ChartTerm must be before than end date.");
        }
        $this->assertTrue($exception);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $endDateValue = '2017-12-30';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation->setEndDate($endDate);
        $res = $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);

        $idValue = '0023456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $startDateValue = '2017-12-24';
        $startDate = new ChartTermDate($startDateValue);
        $chartTermAggregation->setStartDate($startDate);
        $res = $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $chartRankings = $chartTermAggregation->chartRankings();
        $chartTermAggregation->removeChartRanking($chartRankings[3]);
        $chartTermAggregation->removeChartRanking($chartRankings[5]);
        $res = $chartTermRepository->modifyProvision($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $chartRankings = $chartTermAggregation->chartRankings();
        $this->assertEquals(count($chartRankings), 6);
    }

    public function testDelete()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->delete($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't delete provisioned ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->delete($entityId, $chartTermSpecification);
        $this->assertTrue($res);
    }

    public function testRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->release($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->release($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartTermEntity because provisioned ChartTerm doesn't exist.");
        }
        $this->assertTrue($exception);

        $before = [
            'id'            =>  '0013456789abcdef0123456789abcdef',
            'chart_id'      =>  '0a1b2c3d4e5f',
            'start_date'    =>  '2017-12-10',
            'end_date'      =>  '2017-12-16'
        ];

        ProvisionedChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  '0a1b2c3d4e5f',
            'end_date'      =>  '2017-12-02'
        ]);
        $idValue = $before['id'];
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->release($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        ProvisionedChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  '0a1b2c3d4e5f',
            'end_date'      =>  '2017-12-23'
        ]);
        $idValue = $before['id'];
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->release($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't release ChartTermEntity because same provisioned ChartTerm is existing.");
        }
        $this->assertTrue($exception);

        ProvisionedChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  $before['chart_id'],
            'start_date'    =>  $before['start_date'],
            'end_date'      =>  $before['end_date']
        ]);

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->release($entityId, $chartTermSpecification);
        $this->assertTrue($res);
    }

    public function testModifyRelease()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermFactory = app($this->chartTermFactoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationProvision($entityId);
        $exception = false;
        try {
            $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $chartTermEntity = $chartTermFactory->create(
            '00000000000000000000000000000000',
            '0a1b2c3d4e5f',
            '2017-11-26',
            '2017-12-02'
        );
        $chartTermAggregation = $chartTermFactory->toAggregation($chartTermEntity);
        $exception = false;
        try {
            $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartTermEntity because released ChartTerm doesn't exist.");
        }
        $this->assertTrue($exception);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $endDateValue = '2017-12-16';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $endDateValue = '2017-12-09';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't modify released ChartTermEntity because released ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $startDateValue = '2017-12-30';
        $endDateValue = '2017-12-24';
        $startDate = new ChartTermDate($startDateValue);
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation
            ->setStartDate($startDate)
            ->setEndDate($endDate);
        $exception = false;
        try {
            $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Start date of ChartTerm must be before than end date.");
        }
        $this->assertTrue($exception);

        $idValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $endDateValue = '2017-12-30';
        $endDate = new ChartTermDate($endDateValue);
        $chartTermAggregation->setEndDate($endDate);
        $res = $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);

        $idValue = '0123456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $startDateValue = '2017-12-24';
        $startDate = new ChartTermDate($startDateValue);
        $chartTermAggregation->setStartDate($startDate);
        $res = $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $chartRankings = $chartTermAggregation->chartRankings();
        $chartTermAggregation->removeChartRanking($chartRankings[3]);
        $chartTermAggregation->removeChartRanking($chartRankings[5]);
        $res = $chartTermRepository->modifyRelease($chartTermAggregation, $chartTermSpecification);
        $this->assertTrue($res);
        $chartTermAggregation = $chartTermRepository->findAggregationRelease($entityId);
        $chartRankings = $chartTermAggregation->chartRankings();
        $this->assertEquals(count($chartRankings), 6);
    }

    public function testRollback()
    {
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);
        $chartTermSpecification = new ChartTermSpecification();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ChartRanking::class, 64)->create();

        $idValue = '0013456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->rollback($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        $idValue = '00000000000000000000000000000000';
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->rollback($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartTermEntity because released ChartTerm doesn't exist.");
        }
        $this->assertTrue($exception);

        $before = [
            'id'            =>  '0113456789abcdef0123456789abcdef',
            'chart_id'      =>  '0a1b2c3d4e5f',
            'start_date'    =>  '2017-11-26',
            'end_date'      =>  '2017-12-02'
        ];

        ChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  '0a1b2c3d4e5f',
            'end_date'      =>  '2017-12-16'
        ]);
        $idValue = $before['id'];
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->rollback($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartTermEntity because provisioned ChartTerm is already existing.");
        }
        $this->assertTrue($exception);

        ChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  '0a1b2c3d4e5f',
            'end_date'      =>  '2017-12-09'
        ]);
        $idValue = $before['id'];
        $entityId = new EntityId($idValue);
        $exception = false;
        try {
            $chartTermRepository->rollback($entityId, $chartTermSpecification);
        } catch(\Exception $e) {
            $exception = true;
            $this->assertTrue($e instanceof ChartTermException);
            $this->assertEquals($e->getMessage(), "Couldn't rollback ChartTermEntity because same released ChartTerm is existing.");
        }
        $this->assertTrue($exception);

        ChartTerm::where(['id' => $before['id']])->update([
            'chart_id'      =>  $before['chart_id'],
            'start_date'    =>  $before['start_date'],
            'end_date'      =>  $before['end_date']
        ]);

        $idValue = '0113456789abcdef0123456789abcdef';
        $entityId = new EntityId($idValue);
        $res = $chartTermRepository->rollback($entityId, $chartTermSpecification);
        $this->assertTrue($res);
    }

}
