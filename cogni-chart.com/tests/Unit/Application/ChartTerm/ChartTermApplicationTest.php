<?php

namespace Tests\Unit\Application\ChartTerm;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ProvisionedChart;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;
use Mockery;
use Event;
use App\Application\ChartTerm\ChartTermApplication;
use App\Application\DXO\ChartDXO;
use App\Application\DXO\ChartTermDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\ChartTerm\ChartTermList;
use App\Domain\ChartTerm\ChartTermAggregation;

class ChartTermApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $chartTermApplicationInterfaceName = 'App\Application\ChartTerm\ChartTermApplicationInterface';
    private $chartRepositoryInterfaceName = 'App\Domain\Chart\ChartRepositoryInterface';
    private $chartTermListRepositoryInterfaceName = 'App\Domain\ChartTerm\ChartTermListRepositoryInterface';
    private $chartTermRepositoryInterfaceName = 'App\Domain\ChartTerm\ChartTermRepositoryInterface';
    private $chartTermFactoryInterfaceName = 'App\Domain\ChartTerm\ChartTermFactoryInterface';
    private $chartRankingItemApplicationInterfaceName = 'App\Application\ChartRankingItem\ChartRankingItemApplicationInterface';
    private $abstractArtistMusicApplicationInterfaceName = 'App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface';

    private function chartTermFactoryMock()
    {
        return Mockery::mock('App\Domain\ChartTerm\ChartTermFactory')->makePartial();
    }

    private function chartTermRepositoryMock()
    {
        return Mockery::mock(
            'App\Infrastructure\Repositories\ChartTermRepository',
            [
                app($this->redisDAOInterfaceName),
                app($this->chartTermFactoryInterfaceName)
            ]
        )->makePartial();
    }

    public function setUp()
    {
        parent::setUp();

        factory(Chart::class, 3)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChartTerm::class, 8)->create();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        Mockery::close();

        Chart::truncate();
        ProvisionedChart::truncate();
        ChartTerm::truncate();
        ProvisionedChartTerm::truncate();
    }

    public function testProvider()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);
        $this->assertEquals(get_class($chartTermApplication), ChartTermApplication::class);
    }

    public function testListEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '';
        $phaseValue = Phase::released;
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->list($chartIdValue, $phaseValue);
        $result = $chartTermApplication->list($chartTermDXO);
        $this->assertNull($result);

        $chartIdValue = '0a1b2c3d4e5f';
        $phaseValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->list($chartIdValue, $phaseValue);
        $result = $chartTermApplication->list($chartTermDXO);
        $this->assertNull($result);
    }

    public function testList()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $phaseValue = Phase::released;
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->list($chartIdValue, $phaseValue);
        $result = $chartTermApplication->list($chartTermDXO);
        $this->assertEquals($result->chartTermCount(), 2);

        $chartIdValue = '0a1b2c3d4e5f';
        $phaseValue = Phase::provisioned;
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->list($chartIdValue, $phaseValue);
        $result = $chartTermApplication->list($chartTermDXO);
        $this->assertEquals($result->chartTermCount(), 2);
    }

    public function testAggregationEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->aggregation($chartIdValue, $endDateValue);
        $result = $chartTermApplication->aggregation($chartTermDXO);
        $this->assertNull($result);

        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->aggregation($chartIdValue, $endDateValue);
        $result = $chartTermApplication->aggregation($chartTermDXO);
        $this->assertNull($result);
    }

    public function testAggregation()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-16';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->aggregation($chartIdValue, $endDateValue);
        $result = $chartTermApplication->aggregation($chartTermDXO);
        $this->assertNull($result);

        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->aggregation($chartIdValue, $endDateValue);
        $result = $chartTermApplication->aggregation($chartTermDXO);
        $this->assertEquals($result->chartId()->value(), $chartIdValue);
        $this->assertEquals($result->endDate()->value(), $endDateValue);
    }

    public function testMasterAggregationEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $phaseValue = '';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($result);

        $phaseValue = Phase::released;
        $chartIdValue = '';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($result);

        $phaseValue = Phase::released;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($result);
    }

    public function testMasterAggregation()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $phaseValue = Phase::released;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertEquals($result->chartId()->value(), $chartIdValue);
        $this->assertEquals($result->endDate()->value(), $endDateValue);

        $phaseValue = Phase::released;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-16';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($result);

        $phaseValue = Phase::provisioned;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-16';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertEquals($result->chartId()->value(), $chartIdValue);
        $this->assertEquals($result->endDate()->value(), $endDateValue);

        $phaseValue = Phase::provisioned;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($result);
    }

    public function testRegisterEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $result = $chartTermApplication->register($chartTermDXO);
        $this->assertFalse($result);

        $chartIdValue = '00000000000000000000000000000000';
        $startDateValue = '';
        $endDateValue = '2017-11-25';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $result = $chartTermApplication->register($chartTermDXO);
        $this->assertFalse($result);


        $chartIdValue = '00000000000000000000000000000000';
        $startDateValue = '2017-11-19';
        $endDateValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $result = $chartTermApplication->register($chartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartTerm\ChartTermException
     */
    public function testRegisterEmptyChartRankings()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $chartTermApplication->register($chartTermDXO);
    }

    public function testRegisterRepositoryReturnFalse()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('register')->andReturn(false);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $chartIdValue = '0a1b2c3d4e5f';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $chartTermDXO->addRanking(1, '0123456789abcdef0123456789abcdef');
        $result = $chartTermApplication->register($chartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartTerm\ChartTermException
     */
    public function testRegisterExceptionOccurred()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $startDateValue = '2017-11-26';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $chartTermDXO->addRanking(1, '0123456789abcdef0123456789abcdef');
        $chartTermApplication->register($chartTermDXO);
    }

    public function testRegister()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $startDateValue = '2017-11-19';
        $endDateValue = '2017-11-25';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartIdValue, $startDateValue, $endDateValue);
        $chartTermDXO->addRanking(1, '0123456789abcdef0123456789abcdef');
        $result = $chartTermApplication->register($chartTermDXO);
        $this->assertTrue($result);
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        $registeredEntity = $chartTermRepository->getRelease($chartTermBusinessId);
        $this->assertNull($registeredEntity);
        $registeredEntity = $chartTermRepository->getProvision($chartTermBusinessId);
        $this->assertEquals($registeredEntity->chartId()->value(), $chartIdValue);
        $this->assertEquals($registeredEntity->startDate()->value(), $startDateValue);
        $this->assertEquals($registeredEntity->endDate()->value(), $endDateValue);
    }

    public function testDeleteEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->delete($entityIdValue);
        $result = $chartTermApplication->delete($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testDeleteRepositoryReturnFalse()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('delete')->andReturn(false);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->delete($entityIdValue);
        $result = $chartTermApplication->delete($chartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartTerm\ChartTermException
     */
    public function testDeleteExceptionOccurred()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->delete($entityIdValue);
        $chartTermApplication->delete($chartTermDXO);
    }

    public function testDelete()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->delete($entityIdValue);
        $result = $chartTermApplication->delete($chartTermDXO);
        $this->assertTrue($result);
        $chartTermDXO = new ChartTermDXO();
        $phaseValue = Phase::provisioned;
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-16';
        $chartTermDXO->masterAggregation($phaseValue, $chartIdValue, $endDateValue);
        $deleted = $chartTermApplication->masterAggregation($chartTermDXO);
        $this->assertNull($deleted);
    }

    public function testReleaseEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $result = $chartTermApplication->release($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testReleaseRepositoryReturnFalse()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('release')->andReturn(false);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $result = $chartTermApplication->release($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testReleaseReleasedEntityNotFound()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $result = $chartTermApplication->release($chartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartTerm\ChartTermException
     */
    public function testReleaseExceptionOccurred()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $chartTermApplication->release($chartTermDXO);
    }

    public function testRelease()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished)
            {
                $eventName = 'App\Events\ChartTermReleased';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        $eventPublished = false;
        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $result = $chartTermApplication->release($chartTermDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $entityId = $chartTermDXO->getEntityId();
        $releasedEntity = $chartTermRepository->findRelease($entityId);
        $this->assertEquals($releasedEntity->id()->value(), $entityIdValue);

        $eventPublished = false;
        $entityIdValue = '2013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->release($entityIdValue);
        $result = $chartTermApplication->release($chartTermDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testRollbackEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $result = $chartTermApplication->rollback($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRepositoryReturnFalse()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('rollback')->andReturn(false);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $result = $chartTermApplication->rollback($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRollbackedEntityNotFound()
    {
        $chartTermRepositoryMock = $this->chartTermRepositoryMock();
        $chartTermRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $chartTermApplication = new ChartTermApplication(
            app($this->chartRepositoryInterfaceName),
            app($this->chartTermListRepositoryInterfaceName),
            $chartTermRepositoryMock,
            app($this->chartTermFactoryInterfaceName),
            app($this->chartRankingItemApplicationInterfaceName),
            app($this->abstractArtistMusicApplicationInterfaceName)
        );

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $result = $chartTermApplication->rollback($chartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\ChartTerm\ChartTermException
     */
    public function testRollbackExceptionOccurred()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $chartTermApplication->rollback($chartTermDXO);
    }

    public function testRollback()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished)
            {
                $eventName = 'App\Events\ChartTermRollbacked';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        $eventPublished = false;
        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $result = $chartTermApplication->rollback($chartTermDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $entityId = $chartTermDXO->getEntityId();
        $rollbackedEntity = $chartTermRepository->findProvision($entityId);
        $this->assertEquals($rollbackedEntity->id()->value(), $entityIdValue);

        $eventPublished = false;
        $entityIdValue = '2113456789abcdef0123456789abcdef';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->rollback($entityIdValue);
        $result = $chartTermApplication->rollback($chartTermDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testRefreshCachedAggregationEmptyParameters()
    {
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);

        $entityIdValue = '';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->refreshCachedAggregation($chartTermDXO);
        $this->assertFalse($result);

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartIdValue = '';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->refreshCachedAggregation($chartTermDXO);
        $this->assertFalse($result);

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $result = $chartTermApplication->refreshCachedAggregation($chartTermDXO);
        $this->assertFalse($result);
    }

    public function testRefreshCachedAggregation()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartTermApplication = app($this->chartTermApplicationInterfaceName);
        $chartTermRepository = app($this->chartTermRepositoryInterfaceName);

        $entityIdValue = '0113456789abcdef0123456789abcdef';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        $cacheKey = $chartTermRepository->cacheKey($chartTermBusinessId, ChartTermAggregation::class);
        $redisDAO->set($cacheKey, '1');
        $result = $chartTermApplication->refreshCachedAggregation($chartTermDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $cachedEntity = unserialize($cache);
        $this->assertEquals($cachedEntity->id()->value(), $entityIdValue);
        $this->assertEquals($cachedEntity->chartId()->value(), $chartIdValue);
        $this->assertEquals($cachedEntity->endDate()->value(), $endDateValue);

        $entityIdValue = '0013456789abcdef0123456789abcdef';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-16';
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->refreshCachedAggregation($entityIdValue, $chartIdValue, $endDateValue);
        $chartTermBusinessId = $chartTermDXO->getBusinessId();
        $cacheKey = $chartTermRepository->cacheKey($chartTermBusinessId, ChartTermAggregation::class);
        $redisDAO->set($cacheKey, '1');
        $result = $chartTermApplication->refreshCachedAggregation($chartTermDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $this->assertNull($cache);
    }

}
