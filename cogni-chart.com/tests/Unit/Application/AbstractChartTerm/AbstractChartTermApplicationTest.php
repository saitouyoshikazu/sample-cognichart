<?php

namespace Tests\Unit\Application\AbstractChartTerm;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Event;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ProvisionedChart;
use App\Infrastructure\Eloquents\ChartRankingItem;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartTermDate;
use App\Application\AbstractChartTerm\AbstractChartTermApplication;
use App\Application\ChartRankingItem\ChartRankingItemApplication;
use App\Application\ChartTerm\ChartTermApplication;
use App\Application\DXO\AbstractChartTermDXO;
use App\Domain\AbstractChartTerm\AbstractChartTerm;
use App\Domain\ChartRankingItem\ChartRankingItemException;

class AbstractChartTermApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $abstractChartTermApplicationInterfaceName = 'App\Application\AbstractChartTerm\AbstractChartTermApplicationInterface';

    public function setUp()
    {
        parent::setUp();

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartRankingItem::class, 10)->create();
    }

    public function tearDown()
    {
        Mockery::close();

        $storage = app('App\Infrastructure\Storage\AbstractChartTermStorageInterface');
        $files = $storage->files();
        if (!empty($files)) {
            foreach ($files AS $file) {
                $storage->delete($file);
            }
        }

        Chart::truncate();
        ProvisionedChart::truncate();
        ChartRankingItem::truncate();
    }

    private function chartTermApplicationMock()
    {
        return Mockery::mock(
            ChartTermApplication::class,
            [
                app('App\Domain\Chart\ChartRepositoryInterface'),
                app('App\Domain\ChartTerm\ChartTermListRepositoryInterface'),
                app('App\Domain\ChartTerm\ChartTermRepositoryInterface'),
                app('App\Domain\ChartTerm\ChartTermFactoryInterface'),
                app('App\Application\ChartRankingItem\ChartRankingItemApplicationInterface'),
                app('App\Application\AbstractArtistMusic\AbstractArtistMusicApplicationInterface')
            ]
        )->makePartial();
    }

    private function chartRankingItemApplicationMock()
    {
        return Mockery::mock(
            ChartRankingItemApplication::class,
            [
                app('App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface'),
                app('App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface')
            ]
        )->makePartial();
    }

    private function abstractChartTerm()
    {
        $chartIdValue = '0a1b2c3d4e5f';
        $dt = new \DatetimeImmutable();
        $endDate = $dt->add(new \DateInterval('P2W'));
        $endDateValue = $endDate->format('Y-m-d');
        $startDate = $endDate->sub(new \DateInterval('P6D'));
        $startDateVlaue = $startDate->format('Y-m-d');

        return new AbstractChartTerm(
            new EntityId($chartIdValue),
            new ChartTermDate($startDateVlaue),
            new ChartTermDate($endDateValue)
        );
    }

    public function testProvider()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);
        $this->assertEquals(get_class($abstractChartTermApplication), AbstractChartTermApplication::class);
    }

    public function testCreateEmptyParameters()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $countryIdValue = '';
        $chartNameValue = 'Country Does Not Exist';
        $targetDateValue = '';
        $intervalValue = '';
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue, $targetDateValue, $intervalValue);
        $result = $abstractChartTermApplication->create($abstractChartTermDXO);
        $this->assertFalse($result);

        $countryIdValue = 'ZZ';
        $chartNameValue = '';
        $targetDateValue = '';
        $intervalValue = '';
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue, $targetDateValue, $intervalValue);
        $result = $abstractChartTermApplication->create($abstractChartTermDXO);
        $this->assertFalse($result);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testCreateChartDoesNotExist()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $countryIdValue = 'ZZ';
        $chartNameValue = 'Country Does Not Exist';
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue);
        $abstractChartTermApplication->create($abstractChartTermDXO);
    }

    public function testCreateAbstractChartTermsEmpty()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue);

        $domAnalyzerMock = Mockery::mock('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer')->makePartial();
        $domAnalyzerMock->shouldReceive('getStartDateTime')->andReturn(null);
        app()->instance('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer', $domAnalyzerMock);

        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use(&$eventPublished) {
                $eventPublished = true;
            }
        );

        $result = $abstractChartTermApplication->create($abstractChartTermDXO);
        $this->assertTrue($result);
        $this->assertFalse($eventPublished);
    }

    public function testCreate()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->create($countryIdValue, $chartNameValue);

        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use(&$eventPublished) {
                $eventNamespace = 'App\Events\AbstractChartTermCreated';
                if ($event instanceof $eventNamespace) {
                    $eventPublished = true;
                }
            }
        );

        $result = $abstractChartTermApplication->create($abstractChartTermDXO);
        $this->assertTrue($result);
        $this->assertTrue($eventPublished);
    }

    public function testImportEmptyParameters()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);
        $abstractChartTerm = $this->abstractChartTerm();

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import('', $abstractChartTerm->endDate()->value());
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertFalse($result);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), '');
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertFalse($result);
    }

    public function testImportAbstractChartTermDoesNotExist()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $abstractChartTerm = $this->abstractChartTerm();
        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());

        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertFalse($result);
    }

    public function testImportRankingEmpty()
    {
        $abstractChartTermApplication = app($this->abstractChartTermApplicationInterfaceName);

        $abstractChartTerm = $this->abstractChartTerm();
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertFalse($result);
    }

    public function testImportChartRankingItemAlreadyExist()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(true);

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            app('App\Application\ChartRankingItem\ChartRankingItemApplicationInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $eventPublished = false;
        Event::shouldReceive('dispatch')->andReturnUsing(
            function ($event) use(&$eventPublished) {
                $eventName = 'App\Events\ChartRankingItemCreated';
                if ($event instanceof $eventName) {
                    $eventPublished = true;
                }
            }
        );

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertFalse($eventPublished);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testImportChartRankingItemRegisterFalseThreeTimes()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(true);

        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('register')->andReturnUsing(
            function ($chartRankingItemDXO) {
                return false;
            }
        );

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            $chartRankingItemApplicationMock,
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $abstractChartTermApplication->import($abstractChartTermDXO);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testImportChartRankingItemRegisterExceptionThreeTimes()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(true);

        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('exists')->andReturn(false);

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            $chartRankingItemApplicationMock,
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $abstractChartTermApplication->import($abstractChartTermDXO);
    }

    public function testImportChartRankingItemRegisterFalseTwiceTimes()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(true);

        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('exists')->andReturn(false);
        $chartRankingItemApplicationMock->shouldReceive('register')->andReturnUsing(
            function ($chartRankingItemDXO) {
                static $callCount = 0;
                if ($callCount < 2) {
                    $callCount++;
                    return false;
                }
                $callCount++;
                return true;
            }
        );

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            $chartRankingItemApplicationMock,
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertTrue($result);
    }

    public function testImportChartRankingItemRegisterExceptionTwiceTimes()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(true);

        $chartRankingItemApplicationMock = $this->chartRankingItemApplicationMock();
        $chartRankingItemApplicationMock->shouldReceive('exists')->andReturn(false);
        $chartRankingItemApplicationMock->shouldReceive('register')->andReturnUsing(
            function ($chartRankingItemDXO) {
                static $callCount = 0;
                if ($callCount < 2) {
                    $callCount++;
                    throw new ChartRankingItemException("Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.");
                }
                $callCount++;
                return true;
            }
        );

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            $chartRankingItemApplicationMock,
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Ed Sheeran';
        $chartMusicValue = 'Shape Of You';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertTrue($result);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testImportChartTermRegisterFalse()
    {
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $chartTermApplicationMock->shouldReceive('register')->andReturn(false);

        $abstractChartTermApplication = new AbstractChartTermApplication(
            $chartTermApplicationMock,
            app('App\Application\ChartRankingItem\ChartRankingItemApplicationInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface'),
            app('App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface')
        );

        Event::shouldReceive('dispatch')->andReturn();

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $abstractChartTermApplication->import($abstractChartTermDXO);
    }

    public function testImport()
    {
        $abstractChartTermApplication = app('App\Application\AbstractChartTerm\AbstractChartTermApplicationInterface');

        Event::shouldReceive('dispatch')->andReturn();

        $abstractChartTerm = $this->abstractChartTerm();
        $chartArtistValue = 'Halsey';
        $chartMusicValue = 'Bad At Love';
        $abstractChartTerm->addRanking(1, $chartArtistValue, $chartMusicValue);
        $abstractChartTermRepository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
        $abstractChartTermRepository->register($abstractChartTerm);

        $abstractChartTermDXO = new AbstractChartTermDXO();
        $abstractChartTermDXO->import($abstractChartTerm->chartId()->value(), $abstractChartTerm->endDate()->value());
        $result = $abstractChartTermApplication->import($abstractChartTermDXO);
        $this->assertTrue($result);
    }

}
