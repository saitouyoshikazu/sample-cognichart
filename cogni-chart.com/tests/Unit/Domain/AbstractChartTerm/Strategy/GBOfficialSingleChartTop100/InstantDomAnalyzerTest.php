<?php

namespace Tests\Unit\Domain\AbstractChartTerm\Strategy\GBOfficialSinglesChartTop100;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Infrastructure\Eloquents\Chart;
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
        factory(Chart::class, 3)->create();

        $chartRepository = app("App\Domain\Chart\ChartRepositoryInterface");
        $chartTermRepository = app("App\Domain\ChartTerm\ChartTermRepositoryInterface");
        $abstractChartTermRepository = app("App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface");
        $disk = Storage::disk('AbstractChartTermTest');

        $abstractChartTerms = [];

        $chartBusinessId = new ChartBusinessId(new CountryId("GB"), new ChartName("Official Singles Chart Top 100"));
        $chartEntity = $chartRepository->getRelease($chartBusinessId);

        $strategyFactory = new StrategyFactory();
        $requestSender = $strategyFactory->createRequestSender($chartEntity);
        $domAnalyzer = $strategyFactory->createDomAnalyzer($chartEntity);
        $adoptionCriteria = $strategyFactory->createAdoptionCriteria($chartEntity);

        $endDateTime = null;

        $publisherChartTerm = $disk->get("OfficialSinglesChartTop100_OfficialCharts.html");

        $domAnalyzer->setDocument($publisherChartTerm);

        $chartStartDatetime = $domAnalyzer->getStartDateTime();
        $chartEndDatetime = $domAnalyzer->getEndDateTime();
        $this->assertEquals($chartStartDatetime->format('Y-m-d'), '2019-06-22');
        $this->assertEquals($chartEndDatetime->format('Y-m-d'), '2019-06-28');

        $abstractChartTerm = new AbstractChartTerm(
            $chartEntity->id(),
            new ChartTermDate($chartStartDatetime->format('Y-m-d')),
            new ChartTermDate($chartEndDatetime->format('Y-m-d'))
        );
        $endDateTime = $domAnalyzer->getNextEndDateTime();

        foreach ($domAnalyzer AS $ranking => $row) {
            $abstractChartTerm->addRanking($row['ranking'], $row['chart_artist'], $row['chart_music']);
        }
        $res = $abstractChartTermRepository->register($abstractChartTerm);
        $this->assertTrue($res);
    }

}
