<?php

namespace Tests\Unit\Domain\AbstractChartTerm\Strategy\USUSASinglesChart;
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

        $chartBusinessId = new ChartBusinessId(new CountryId("US"), new ChartName("USA Singles Chart"));
        $chartEntity = $chartRepository->getRelease($chartBusinessId);

        $strategyFactory = new StrategyFactory();
        $requestSender = $strategyFactory->createRequestSender($chartEntity);
        $domAnalyzer = $strategyFactory->createDomAnalyzer($chartEntity);
        $adoptionCriteria = $strategyFactory->createAdoptionCriteria($chartEntity);

        $endDateTime = null;

//        $publisherChartTerm = $disk->get("Billboard_Hot_100_2018-12-29.html");
        $publisherChartTerm = $disk->get("Billboard_Hot_100_2019-10-01.html");

        $domAnalyzer->setDocument($publisherChartTerm);

        $chartStartDatetime = $domAnalyzer->getStartDateTime();
        $chartEndDatetime = $domAnalyzer->getEndDateTime();
        $this->assertNotEmpty($chartStartDatetime);
        $this->assertNotEmpty($chartEndDatetime);

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
