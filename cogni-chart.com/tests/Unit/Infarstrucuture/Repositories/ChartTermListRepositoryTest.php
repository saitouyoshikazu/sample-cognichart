<?php

namespace Tests\Unit\Infrastructure\Repositories;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Domain\EntityId;
use App\Domain\ValueObjects\Phase;
use App\Domain\ChartTerm\ChartTermList;

class ChartTermListRepositoryTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $chartTermListRepositoryInterfaceName = 'App\Domain\ChartTerm\ChartTermListRepositoryInterface';

    public function testProvider()
    {
        $chartTermListRepository = app($this->chartTermListRepositoryInterfaceName);
        $this->assertEquals(get_class($chartTermListRepository), 'App\Infrastructure\Repositories\ChartTermListRepository');
    }

    public function testReleasedChartTermList()
    {
        $chartTermListRepository = app($this->chartTermListRepositoryInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);
        $res = $chartTermListRepository->releasedChartTermList($chartId);
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $verify = [
            '0123456789abcdef0123456789abcdef',
            '0113456789abcdef0123456789abcdef',
        ];

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);
        $res = $chartTermListRepository->releasedChartTermList($chartId);
        $this->assertEquals($res->chartTermCount(), 2);
        $this->assertEquals($res->phase(), Phase::released);
        $result = [];
        foreach ($res AS $chartTermEntity) {
            $result[] = $chartTermEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
    }

    public function testProvisionedChartTermList()
    {
        $chartTermListRepository = app($this->chartTermListRepositoryInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);
        $res = $chartTermListRepository->provisionedChartTermList($chartId);
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $verify = [
            '0023456789abcdef0123456789abcdef',
            '0013456789abcdef0123456789abcdef',
        ];

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);
        $res = $chartTermListRepository->provisionedChartTermList($chartId);
        $this->assertEquals($res->chartTermCount(), 2);
        $this->assertEquals($res->phase(), Phase::provisioned);
        $result = [];
        foreach ($res AS $chartTermEntity) {
            $result[] = $chartTermEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
    }

    public function testChartTermList()
    {
        $chartTermListRepository = app($this->chartTermListRepositoryInterfaceName);

        $chartIdValue = '0a1b2c3d4e5f';
        $chartId = new EntityId($chartIdValue);

        $phase = new Phase('released');
        $res = $chartTermListRepository->chartTermList($chartId, $phase);
        $this->assertNull($res);

        $phase = new Phase('provisioned');
        $res = $chartTermListRepository->chartTermList($chartId, $phase);
        $this->assertNull($res);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ChartTerm::class, 8)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChartTerm::class, 8)->create();

        $phase = new Phase('released');
        $verify = [
            '0123456789abcdef0123456789abcdef',
            '0113456789abcdef0123456789abcdef',
        ];
        $res = $chartTermListRepository->chartTermList($chartId, $phase);
        $this->assertEquals($res->phase(), Phase::released);
        $this->assertEquals($res->chartTermCount(), 2);
        $result = [];
        foreach ($res AS $chartTermEntity) {
            $result[] = $chartTermEntity->id()->value();
        }
        $this->assertEquals($result, $verify);

        $phase = new Phase('provisioned');
        $verify = [
            '0023456789abcdef0123456789abcdef',
            '0013456789abcdef0123456789abcdef',
        ];
        $res = $chartTermListRepository->chartTermList($chartId, $phase);
        $this->assertEquals($res->chartTermCount(), 2);
        $this->assertEquals($res->phase(), Phase::provisioned);
        $result = [];
        foreach ($res AS $chartTermEntity) {
            $result[] = $chartTermEntity->id()->value();
        }
        $this->assertEquals($result, $verify);
    }

}
