<?php

namespace Tests\Unit\Domain\AbstractChartTerm;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Storage;
use App\Domain\ValueObjects\ChartName;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\Country\CountryId;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\AbstractChartTerm\AbstractChartTerm;
use App\Domain\AbstractChartTerm\AbstractChartTermService;
use App\Domain\AbstractChartTerm\AbstractChartTermSpecification;
use App\Domain\AbstractChartTerm\Strategy\StrategyFactory;
use App\Application\DXO\ChartTermDXO;

class AbstractChartTermServiceTest extends TestCase
{

    use RefreshDatabase, DatabaseMigrations;

    private $abstractChartTermServiceInterfaceName = 'App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface';
    private $redisDAOInterfaceName = 'App\Infrastructure\RedisDAO\RedisDAOInterface';
    private $abstractChartTermRepositoryInterfaceName = 'App\Domain\AbstractArtistMusic\AbstractArtistMusicRepositoryInterface';

    public function tearDown()
    {
        Mockery::close();
    }

    public function testProvider()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);
        $this->assertEquals(get_class($abstractChartTermService), AbstractChartTermService::class);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testCreateChartDoesNotExist()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'ZZ';
        $chartNameValue = 'Country Does Not Exist';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);
        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertNull($abstractChartTerms);
    }

    /**
     * @expectedException App\Domain\AbstractChartTerm\AbstractChartTermException
     */
    public function testCreatNoDocument()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $requestSenderMock = Mockery::mock('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\RequestSender')->makePartial();
        $requestSenderMock->shouldReceive('send')->andReturn('');
        app()->instance('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\RequestSender', $requestSenderMock);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
    }

    public function testCreateDomaAnalyzerInvalidStartDate()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $domAnalyzerMock = Mockery::mock('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer')->makePartial();
        $domAnalyzerMock->shouldReceive('getStartDateTime')->andReturn(null);
        app()->instance('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer', $domAnalyzerMock);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }

    public function testCreateDomaAnalyzerInvalidEndDate()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $domAnalyzerMock = Mockery::mock('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer')->makePartial();
        $domAnalyzerMock->shouldReceive('getEndDateTime')->andReturn(null);
        app()->instance('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\DomAnalyzer', $domAnalyzerMock);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }


    public function testCreateChartTermAlreadyImported()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $chartRepository = app('App\Domain\Chart\ChartRepositoryInterface');
        $abstractChartTermSpecification = new AbstractChartTermSpecification();
        $chartEntity = $abstractChartTermSpecification->chartEntity($chartRepository, $chartBusinessId);
        $strategyFactory = new StrategyFactory();
        $requestSender = $strategyFactory->createRequestSender($chartEntity);
        $domAnalyzer = $strategyFactory->createDomAnalyzer($chartEntity);
        $remote = app('App\Infrastructure\Remote\RemoteInterface');

        $document = $requestSender->send($remote);
        $domAnalyzer->setDocument($document);
        $startDateTime = $domAnalyzer->getStartDateTime();
        $endDateTime = $domAnalyzer->getEndDateTime();

        $chartTermApplication = app('App\Application\ChartTerm\ChartTermApplicationInterface');
        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($chartEntity->id()->value(), $startDateTime->format('Y-m-d'), $endDateTime->format('Y-m-d'));
        $chartTermDXO->addRanking(1, 'AAAAABBBBBCCCCDDDDEEEE');
        $chartTermApplication->register($chartTermDXO);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }

    public function testCreateAbstractChartTermAlreadyExist()
    {
        $repositoryMock = Mockery::mock(
            'App\Infrastructure\Repositories\AbstractChartTermRepository',
            [app('App\Infrastructure\Storage\AbstractChartTermStorageInterface')]
        )->makePartial();
        $repositoryMock->shouldReceive('exists')->andReturn(true);

        $abstractChartTermService = new AbstractChartTermService(
            app('App\Domain\Chart\ChartRepositoryInterface'),
            app('App\Domain\ChartTerm\ChartTermRepositoryInterface'),
            $repositoryMock,
            app('App\Infrastructure\Remote\RemoteInterface')
        );

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }

    public function testCreateAdoptionCriteriaFalse()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $adoptionCriteriaMock = Mockery::mock('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\AdoptionCriteria')->makePartial();
        $adoptionCriteriaMock->shouldReceive('judge')->andReturn(false);
        app()->instance('App\Domain\AbstractChartTerm\Strategy\USBillboardHot100\AdoptionCriteria', $adoptionCriteriaMock);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }

    public function testCreateRegisterFalse()
    {
        $repositoryMock = Mockery::mock(
            'App\Infrastructure\Repositories\AbstractChartTermRepository',
            [app('App\Infrastructure\Storage\AbstractChartTermStorageInterface')]
        )->makePartial();
        $repositoryMock->shouldReceive('register')->andReturn(false);

        $abstractChartTermService = new AbstractChartTermService(
            app('App\Domain\Chart\ChartRepositoryInterface'),
            app('App\Domain\ChartTerm\ChartTermRepositoryInterface'),
            $repositoryMock,
            app('App\Infrastructure\Remote\RemoteInterface')
        );

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEmpty($abstractChartTerms);
    }

    public function testCreate()
    {
        $abstractChartTermService = app($this->abstractChartTermServiceInterfaceName);

        factory(\App\Infrastructure\Eloquents\Chart::class, 3)->create();
        factory(\App\Infrastructure\Eloquents\ProvisionedChart::class, 3)->create();

        $countryIdValue = 'US';
        $chartNameValue = 'Billboard Hot 100';
        $countryId = new CountryId($countryIdValue);
        $chartName = new ChartName($chartNameValue);
        $chartBusinessId = new ChartBusinessId($countryId, $chartName);

        $abstractChartTerms = $abstractChartTermService->create($chartBusinessId);
        $this->assertEquals(count($abstractChartTerms), 1);

        if (!empty($abstractChartTerms)) {
            $repository = app('App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface');
            foreach ($abstractChartTerms AS $abstractChartTerm) {
                $repository->delete($abstractChartTerm->businessId());
            }
        }
    }

}
