<?php

namespace Tests\Unit\Domain\AbstractChartTerm\Strategy\AUARIASinglesChart;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\ProvisionedChart;
use Mockery;
use Storage;
use App\Domain\Country\CountryId;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\AbstractChartTerm\Strategy\StrategyFactory;
use App\Domain\AbstractChartTerm\AbstractChartTerm;
use App\Domain\ValueObjects\ChartName;
use App\Domain\ValueObjects\ChartTermDate;

class InstantDomAnalyzerTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    public function testDoInstant()
    {
        factory(ProvisionedChart::class, 3)->create();

        $chartRepository = app("App\Domain\Chart\ChartRepositoryInterface");
        $chartTermRepository = app("App\Domain\ChartTerm\ChartTermRepositoryInterface");
        $abstractChartTermRepository = app("App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface");
        $disk = Storage::disk('AbstractChartTermTest');

        $abstractChartTerms = [];

        $chartBusinessId = new ChartBusinessId(new CountryId("AU"), new ChartName("ARIA Singles Chart"));
        $chartEntity = $chartRepository->getProvision($chartBusinessId);

        $strategyFactory = new StrategyFactory();
        $requestSender = $strategyFactory->createRequestSender($chartEntity);
        $domAnalyzer = $strategyFactory->createDomAnalyzer($chartEntity);
        $adoptionCriteria = $strategyFactory->createAdoptionCriteria($chartEntity);

        $endDateTime = null;

        $publisherChartTerm = $disk->get("ARIA_SinglesChart.html");

        $domAnalyzer->setDocument($publisherChartTerm);

        $currentDateTime = new \DateTimeImmutable(date("Y-m-d"));
        $chartStartDatetime = $domAnalyzer->getStartDateTime();
        $chartEndDatetime = $domAnalyzer->getEndDateTime();
        $this->assertEquals($chartStartDatetime->format('Y-m-d'), $currentDateTime->sub(new \DateInterval("P6D"))->format('Y-m-d'));
        $this->assertEquals($chartEndDatetime->format('Y-m-d'), $currentDateTime->format('Y-m-d'));

        $abstractChartTerm = new AbstractChartTerm(
            $chartEntity->id(),
            new ChartTermDate($chartStartDatetime->format('Y-m-d')),
            new ChartTermDate($chartEndDatetime->format('Y-m-d'))
        );

        foreach ($domAnalyzer AS $ranking => $row) {
            $abstractChartTerm->addRanking($row['ranking'], $row['chart_artist'], $row['chart_music']);
        }

        $res = $abstractChartTermRepository->register($abstractChartTerm);
        $this->assertTrue($res);
    }

}
