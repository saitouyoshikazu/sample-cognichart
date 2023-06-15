<?php

namespace Tests\Unit\Application\Sns;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Event;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\Eloquents\ProvisionedChart;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;
use App\Application\Sns\SnsApplication;
use App\Application\DXO\SnsDXO;

class SnsApplicationTest extends TestCase
{

    use DatabaseMigrations;

    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $snsApplicationInterfaceName = 'App\Application\Sns\SnsApplicationInterface';

    private function bufferMailMock()
    {
        return Mockery::mock('App\Infrastructure\Sns\BufferMail')->makePartial();
    }

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        $redisDAO = app($this->redisDAOInterfaceName);
        $redisDAO->clear('*');
        $redisDAO->resetIsCache();

        Mockery::close();

        Chart::truncate();
        ChartTerm::truncate();
        ProvisionedChart::truncate();
        ProvisionedChartTerm::truncate();
    }

    public function testProvider()
    {
        $snsApplication = app($this->snsApplicationInterfaceName);
        $this->assertEquals(get_class($snsApplication), SnsApplication::class);
    }

    public function testPublishReleasedMessageEmptyParameters()
    {
        $snsApplication = app($this->snsApplicationInterfaceName);

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();

        $coutryIdValue = '';
        $chartNameValue = 'Billboard Hot 100';
        $endDateValue = '2017-12-02';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertFalse($result);

        $coutryIdValue = 'US';
        $chartNameValue = '';
        $endDateValue = '2017-12-02';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertFalse($result);

        $coutryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $endDateValue = '';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertFalse($result);
    }

    public function testPublishReleasedMessageChartDoesNotReleased()
    {
        $bufferMailMock = $this->bufferMailMock();
        $called = false;
        $bufferMailMock->shouldReceive('send')->andReturnUsing(
            function ($message) use (&$called) {
                $called = true;
            }
        );
        $snsApplication = new SnsApplication($bufferMailMock, app('App\Domain\Chart\ChartRepositoryInterface'));

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();

        $coutryIdValue = 'AU';
        $chartNameValue = 'ARIA SINGLES CHART';
        $endDateValue = '2017-12-05';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertTrue($result);
        $this->assertFalse($called);
    }

    public function testPublishReleasedMessageChartTermIsNotLatest()
    {
        $bufferMailMock = $this->bufferMailMock();
        $called = false;
        $bufferMailMock->shouldReceive('send')->andReturnUsing(
            function ($message) use (&$called) {
                $called = true;
            }
        );
        $snsApplication = new SnsApplication($bufferMailMock, app('App\Domain\Chart\ChartRepositoryInterface'));

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();

        $coutryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $endDateValue = '2017-12-02';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertTrue($result);
        $this->assertFalse($called);
    }

    public function testPublishReleasedMessageChartTermDoesNotReleased()
    {
        $bufferMailMock = $this->bufferMailMock();
        $called = false;
        $bufferMailMock->shouldReceive('send')->andReturnUsing(
            function ($message) use (&$called) {
                $called = true;
            }
        );
        $snsApplication = new SnsApplication($bufferMailMock, app('App\Domain\Chart\ChartRepositoryInterface'));

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();

        $coutryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $endDateValue = '2017-12-16';
        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertTrue($result);
        $this->assertFalse($called);
    }

    public function testPublishReleasedMessage()
    {
        $bufferMailMock = $this->bufferMailMock();
        $called = false;
        $correctMessage = false;
        $coutryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $endDateValue = '2017-12-09';
        $bufferMailMock->shouldReceive('send')->andReturnUsing(
            function ($message) use (&$called, &$correctMessage, $chartNameValue, $endDateValue) {
                $called = true;
                if ($message == "We archived {$chartNameValue} {$endDateValue}.") {
                    $correctMessage = true;
                }
                return true;
            }
        );
        $snsApplication = new SnsApplication($bufferMailMock, app('App\Domain\Chart\ChartRepositoryInterface'));

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();

        $snsDXO = new SnsDXO();
        $snsDXO->publishReleasedMessage($coutryIdValue, $chartNameValue, $endDateValue);
        $result = $snsApplication->publishReleasedMessage($snsDXO);
        $this->assertTrue($result);
        $this->assertTrue($called);
        $this->assertTrue($correctMessage);
    }

}
