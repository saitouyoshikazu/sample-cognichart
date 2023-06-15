<?php

namespace Tests\Unit\Application\Chart;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ProvisionedChart;
use Mockery;
use Event;
use App\Application\Chart\ChartApplication;
use App\Application\DXO\ChartDXO;
use App\Domain\ValueObjects\Phase;
use App\Domain\Chart\ChartList;
use App\Domain\Chart\ChartAggregation;

class ChartApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $chartApplicationInterfaceName = 'App\Application\Chart\ChartApplicationInterface';
    private $chartRepositoryInterfaceName = 'App\Domain\Chart\ChartRepositoryInterface';
    private $chartFactoryInterfaceName = 'App\Domain\Chart\ChartFactoryInterface';
    private $chartListRepositoryInterfaceName = 'App\Domain\Chart\ChartListRepositoryInterface';

    private function chartFactoryMock()
    {
        return Mockery::mock('App\Domain\Chart\ChartFactory')->makePartial();
    }

    private function chartRepositoryMock()
    {
        return Mockery::mock(
            'App\Infrastructure\Repositories\ChartRepository',
            [
                app($this->redisDAOInterfaceName),
                app($this->chartFactoryInterfaceName),
                app('App\Domain\ChartTerm\ChartTermListRepositoryInterface')
            ]
        )->makePartial();
    }

    public function setUp()
    {
        parent::setUp();

        factory(Chart::class, 3)->create();
        factory(ProvisionedChart::class, 3)->create();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        Mockery::close();

        Chart::truncate();
        ProvisionedChart::truncate();
    }

    public function testProvider()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);
        $this->assertEquals(get_class($chartApplication), ChartApplication::class);
    }

    public function testListEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $chartDXO = new ChartDXO();
        $chartDXO->list('');
        $result = $chartApplication->list($chartDXO);
        $this->assertNull($result);
    }

    public function testList()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::released);
        $result = $chartApplication->list($chartDXO);
        $this->assertEquals($result->chartCount(), 2);

        $chartDXO = new ChartDXO();
        $chartDXO->list(Phase::provisioned);
        $result = $chartApplication->list($chartDXO);
        $this->assertEquals($result->chartCount(), 2);
    }

    public function testRegisterEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $countryIdValue = '';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);

        $countryIdValue = 'US';
        $chartNameValue = '';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = '';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = 'https';
        $hostValue = '';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);
    }

    public function testRegisterFactoryCreateEmpty()
    {
        $chartFactoryMock = $this->chartFactoryMock();
        $chartFactoryMock->shouldReceive('create')->andReturn(null);
        $chartApplication = new ChartApplication(
            app($this->chartRepositoryInterfaceName),
            $chartFactoryMock,
            app($this->chartListRepositoryInterfaceName)
        );

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);
    }

    public function testRegisterRepositoryReturnFalse()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('register')->andReturn(false);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Chart\ChartException
     */
    public function testRegisterExceptionOccurred()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $chartApplication->register($chartDXO);
    }

    public function testRegister()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 200';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-200';
        $chartDXO = new ChartDXO();
        $chartDXO->register($countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->register($chartDXO);
        $this->assertTrue($result);
        $chartBusinessId = $chartDXO->getBusinessId();
        $chartEntity = $chartRepository->getProvision($chartBusinessId);
        $this->assertEquals($chartEntity->countryId()->value(), $countryIdValue);
        $this->assertEquals($chartEntity->chartName()->value(), $chartNameValue);
        $this->assertEquals($chartEntity->scheme(), $schemeValue);
        $this->assertEquals($chartEntity->host(), $hostValue);
        $this->assertEquals($chartEntity->uri(), $uriValue);
    }

    public function testGetEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $phaseValue = '';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->get($phaseValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->get($chartDXO);
        $this->assertNull($result);

        $phaseValue = Phase::released;
        $countryIdValue = '';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->get($phaseValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->get($chartDXO);
        $this->assertNull($result);

        $phaseValue = Phase::released;
        $countryIdValue = 'US';
        $chartNameValue = '';
        $chartDXO = new ChartDXO();
        $chartDXO->get($phaseValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->get($chartDXO);
        $this->assertNull($result);
    }

    public function testGet()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $phaseValue = Phase::released;
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->get($phaseValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->get($chartDXO);
        $this->assertEquals($result->businessId()->value(), $chartDXO->getBusinessId()->value());

        $phaseValue = Phase::provisioned;
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $chartDXO = new ChartDXO();
        $chartDXO->get($phaseValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->get($chartDXO);
        $this->assertEquals($result->businessId()->value(), $chartDXO->getBusinessId()->value());
    }

    public function testModifyEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $phaseValue = '';
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = '';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = '';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = '';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);
    }

    public function testModifyEntityNotFound()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $chartRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $schemeValue = 'https';
        $hostValue = 'www.ariacharts.com.au';
        $uriValue = 'charts/singles-chart';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);
    }

    public function testModifyRepositoryReturnFalse()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('modifyRelease')->andReturn(false);
        $chartRepositoryMock->shouldReceive('modifyProvision')->andReturn(false);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);

        $phaseValue = Phase::provisioned;
        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $schemeValue = 'https';
        $hostValue = 'www.ariacharts.com.au';
        $uriValue = 'charts/singles-chart';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Chart\ChartException
     */
    public function testModifyExceptionOccurred()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $phaseValue = Phase::released;
        $entityIdValue = 'f5e4d3c2b1a0';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $chartApplication->modify($chartDXO);
    }

    public function testModify()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished) {
                $eventName = 'App\Events\ChartModified';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartApplication = app($this->chartApplicationInterfaceName);

        $eventPublished = false;
        $phaseValue = Phase::released;
        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $schemeValue = 'https';
        $hostValue = 'www.billboard.com';
        $uriValue = 'charts/hot-100';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);

        $eventPublished = false;
        $phaseValue = Phase::provisioned;
        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $schemeValue = 'https';
        $hostValue = 'www.ariacharts.com.au';
        $uriValue = 'charts/singles-chart';
        $chartDXO = new ChartDXO();
        $chartDXO->modify($phaseValue, $entityIdValue, $countryIdValue, $chartNameValue, $schemeValue, $hostValue, $uriValue);
        $result = $chartApplication->modify($chartDXO);
        $this->assertTrue($result);
        $this->assertFalse($eventPublished);
    }

    public function testReleaseEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '';
        $chartDXO = new ChartDXO();
        $chartDXO->release($entityIdValue);
        $result = $chartApplication->release($chartDXO);
        $this->assertFalse($result);
    }

    public function testReleaseRepositoryReturnFalse()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('release')->andReturn(false);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->release($entityIdValue);
        $result = $chartApplication->release($chartDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Chart\ChartException
     */
    public function testReleaseExceptionOccurred()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '0a1b2c3d4e5f';
        $chartDXO = new ChartDXO();
        $chartDXO->release($entityIdValue);
        $chartApplication->release($chartDXO);
    }

    public function testReleaseReleasedEntityNotFound()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('findRelease')->andReturn(null);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->release($entityIdValue);
        $result = $chartApplication->release($chartDXO);
        $this->assertFalse($result);
    }

    public function testRelease()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished)
            {
                $eventName = 'App\Events\ChartReleased';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartApplication = app($this->chartApplicationInterfaceName);
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->release($entityIdValue);
        $result = $chartApplication->release($chartDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $entityId = $chartDXO->getEntityId();
        $releasedEntity = $chartRepository->findProvision($entityId);
        $this->assertNull($releasedEntity);
        $releasedEntity = $chartRepository->findRelease($entityId);
        $this->assertEquals($releasedEntity->id()->value(), $entityIdValue);
    }

    public function testRollbackEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '';
        $chartDXO = new ChartDXO();
        $chartDXO->rollback($entityIdValue);
        $result = $chartApplication->rollback($chartDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRepositoryReturnFalse()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('rollback')->andReturn(false);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $entityIdValue = '0a1b2c3d4e5f';
        $chartDXO = new ChartDXO();
        $chartDXO->rollback($entityIdValue);
        $result = $chartApplication->rollback($chartDXO);
        $this->assertFalse($result);
    }

    public function testRollbackRollbackedEntityNotFound()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('findProvision')->andReturn(null);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $entityIdValue = '0a1b2c3d4e5f';
        $chartDXO = new ChartDXO();
        $chartDXO->rollback($entityIdValue);
        $result = $chartApplication->rollback($chartDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Chart\ChartException
     */
    public function testRollbackExceptionOccurred()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->rollback($entityIdValue);
        $chartApplication->rollback($chartDXO);
    }

    public function testRollback()
    {
        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use (&$eventPublished)
            {
                $eventName = 'App\Events\ChartRollbacked';
                if ($event instanceOf $eventName) {
                    $eventPublished = true;
                }
            }
        );
        $chartApplication = app($this->chartApplicationInterfaceName);
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $entityIdValue = '0a1b2c3d4e5f';
        $chartDXO = new ChartDXO();
        $chartDXO->rollback($entityIdValue);
        $result = $chartApplication->rollback($chartDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
        $entityId = $chartDXO->getEntityId();
        $rollbackedEntity = $chartRepository->findRelease($entityId);
        $this->assertNull($rollbackedEntity);
        $rollbackedEntity = $chartRepository->findProvision($entityId);
        $this->assertEquals($rollbackedEntity->id()->value(), $entityIdValue);
    }

    public function testDeleteEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '';
        $chartDXO = new ChartDXO();
        $chartDXO->delete($entityIdValue);
        $result = $chartApplication->delete($chartDXO);
        $this->assertFalse($result);
    }

    public function testDeleteRepositoryReturnFalse()
    {
        $chartRepositoryMock = $this->chartRepositoryMock();
        $chartRepositoryMock->shouldReceive('delete')->andReturn(false);
        $chartApplication = new ChartApplication(
            $chartRepositoryMock,
            app($this->chartFactoryInterfaceName),
            app($this->chartListRepositoryInterfaceName)
        );

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->delete($entityIdValue);
        $result = $chartApplication->delete($chartDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\Chart\ChartException
     */
    public function testDeleteExceptionOccurred()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '0a1b2c3d4e5f';
        $chartDXO = new ChartDXO();
        $chartDXO->delete($entityIdValue);
        $chartApplication->delete($chartDXO);
    }

    public function testDelete()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $chartDXO = new ChartDXO();
        $chartDXO->delete($entityIdValue);
        $result = $chartApplication->delete($chartDXO);
        $this->assertTrue($result);
        $deletedEntity = $chartRepository->findProvision($chartDXO->getEntityId());
        $this->assertNull($deletedEntity);
    }

    public function testRefreshCachedChartList()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartApplication = app($this->chartApplicationInterfaceName);

        $redisDAO->set(ChartList::class, '1');
        $chartApplication->refreshCachedChartList();
        $cache = $redisDAO->get(ChartList::class);
        $chartList = unserialize($cache);
        $this->assertEquals(get_class($chartList), ChartList::class);
    }

    public function testRefreshCachedAggregationEmptyParameters()
    {
        $chartApplication = app($this->chartApplicationInterfaceName);

        $entityIdValue = '';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($entityIdValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->refreshCachedAggregation($chartDXO);
        $this->assertFalse($result);

        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = '';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($entityIdValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->refreshCachedAggregation($chartDXO);
        $this->assertFalse($result);

        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = '';
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($entityIdValue, $countryIdValue, $chartNameValue);
        $result = $chartApplication->refreshCachedAggregation($chartDXO);
        $this->assertFalse($result);
    }

    public function testRefreshCachedAggregation()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $chartApplication = app($this->chartApplicationInterfaceName);
        $chartRepository = app($this->chartRepositoryInterfaceName);

        $entityIdValue = '0a1b2c3d4e5f';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($entityIdValue, $countryIdValue, $chartNameValue);
        $chartBusinessId = $chartDXO->getBusinessId();
        $cacheKey = $chartRepository->cacheKey($chartBusinessId, ChartAggregation::class);
        $redisDAO->set($cacheKey, '1');
        $result = $chartApplication->refreshCachedAggregation($chartDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $refreshedEntity = unserialize($cache);
        $this->assertEquals($refreshedEntity->id()->value(), $entityIdValue);

        $entityIdValue = 'ff55ee44dd33cc22bb11aa00';
        $countryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $chartDXO = new ChartDXO();
        $chartDXO->refreshCachedAggregation($entityIdValue, $countryIdValue, $chartNameValue);
        $chartBusinessId = $chartDXO->getBusinessId();
        $cacheKey = $chartRepository->cacheKey($chartBusinessId, ChartAggregation::class);
        $redisDAO->set($cacheKey, '1');
        $result = $chartApplication->refreshCachedAggregation($chartDXO);
        $this->assertTrue($result);
        $cache = $redisDAO->get($cacheKey);
        $this->assertNull($cache);
    }

}
