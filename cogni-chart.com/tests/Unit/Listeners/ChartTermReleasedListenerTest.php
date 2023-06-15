<?php

namespace Tests\Unit\Listeners;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use App\Infrastructure\Eloquents\Chart;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\Eloquents\ProvisionedChart;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;
use App\Events\ChartTermReleased;
use App\Listeners\ChartTermReleasedListener;

class ChartTermReleasedListenerTest extends TestCase
{

    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        factory(Chart::class, 3)->create();
        factory(ChartTerm::class, 8)->create();
        factory(ProvisionedChart::class, 3)->create();
        factory(ProvisionedChartTerm::class, 8)->create();
    }

    public function tearDown()
    {
        Mockery::close();

        Chart::truncate();
        ChartTerm::truncate();
        ProvisionedChart::truncate();
        ProvisionedChartTerm::truncate();

        parent::tearDown();
    }

    private function chartApplicationMock()
    {
        return Mockery::mock('App\Application\Chart\ChartApplication')->makePartial();
    }

    private function chartTermApplicationMock()
    {
        return Mockery::mock('App\Application\ChartTerm\ChartTermApplication')->makePartial();
    }

    private function snsApplicationMock()
    {
        return Mockery::mock('App\Application\Sns\SnsApplication')->makePartial();
    }

    public function testHandleChartIsNotReleased()
    {
        $chartApplicationMock = $this->chartApplicationMock();
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $snsApplicationMock = $this->snsApplicationMock();
        $chartRefreshCachedAggregationCalled = false;
        $chartTermRefreshCachedAggregationCalled = false;
        $snsPublishReleasedMessageCalled = false;
        $chartApplicationMock->shouldReceive('refreshCachedAggregation')->andReturnUsing(
            function($chartDXO) use(&$chartRefreshCachedAggregationCalled) {
                $chartRefreshCachedAggregationCalled = true;
            }
        );
        $chartTermApplicationMock->shouldReceive('refreshCachedAggregation')->andReturnUsing(
            function($chartTermDXO) use(&$chartTermRefreshCachedAggregationCalled) {
                $chartTermRefreshCachedAggregationCalled = true;
            }
        );
        $snsApplicationMock->shouldReceive('publishReleasedMessage')->andReturnUsing(
            function($snsDXO) use(&$snsPublishReleasedMessageCalled) {
                $snsPublishReleasedMessageCalled = true;
            }
        );
        $chartTermReleasedListener = new ChartTermReleasedListener(
            $chartApplicationMock,
            $chartTermApplicationMock,
            $snsApplicationMock
        );

        $chartTermIdValue = '2113456789abcdef0123456789abcdef';
        $chartIdValue = 'ff55ee44dd33cc22bb11aa00';
        $endDateValue = '2017-11-21';
        $chartTermReleased = new ChartTermReleased(
            $chartTermIdValue,
            $chartIdValue,
            $endDateValue
        );
        $chartTermReleasedListener->handle($chartTermReleased);
        $this->assertTrue($chartTermRefreshCachedAggregationCalled);
        $this->assertFalse($chartRefreshCachedAggregationCalled);
        $this->assertFalse($snsPublishReleasedMessageCalled);
    }

    public function testHandleChartIsReleased()
    {
        $chartApplicationMock = $this->chartApplicationMock();
        $chartTermApplicationMock = $this->chartTermApplicationMock();
        $snsApplicationMock = $this->snsApplicationMock();
        $chartRefreshCachedAggregationCalled = false;
        $chartTermRefreshCachedAggregationCalled = false;
        $snsPublishReleasedMessageCalled = false;
        $chartApplicationMock->shouldReceive('refreshCachedAggregation')->andReturnUsing(
            function($chartDXO) use(&$chartRefreshCachedAggregationCalled) {
                $chartRefreshCachedAggregationCalled = true;
            }
        );
        $chartTermApplicationMock->shouldReceive('refreshCachedAggregation')->andReturnUsing(
            function($chartTermDXO) use(&$chartTermRefreshCachedAggregationCalled) {
                $chartTermRefreshCachedAggregationCalled = true;
            }
        );
        $snsApplicationMock->shouldReceive('publishReleasedMessage')->andReturnUsing(
            function($snsDXO) use(&$snsPublishReleasedMessageCalled) {
                $snsPublishReleasedMessageCalled = true;
            }
        );
        $chartTermReleasedListener = new ChartTermReleasedListener(
            $chartApplicationMock,
            $chartTermApplicationMock,
            $snsApplicationMock
        );

        $chartTermIdValue = '0113456789abcdef0123456789abcdef';
        $chartIdValue = '0a1b2c3d4e5f';
        $endDateValue = '2017-12-02';
        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $chartTermReleased = new ChartTermReleased(
            $chartTermIdValue,
            $chartIdValue,
            $endDateValue,
            $countryIdValue,
            $chartNameValue
        );
        $chartTermReleasedListener->handle($chartTermReleased);
        $this->assertTrue($chartTermRefreshCachedAggregationCalled);
        $this->assertTrue($chartRefreshCachedAggregationCalled);
        $this->assertTrue($snsPublishReleasedMessageCalled);
    }

}
