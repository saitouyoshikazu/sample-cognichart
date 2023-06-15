<?php

namespace App\Domain\AbstractChartTerm;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\AbstractChartTerm\Strategy\StrategyFactory;

class AbstractChartTermService implements AbstractChartTermServiceInterface
{

    private $chartRepository;
    private $chartTermRepository;
    private $abstractChartTermRepository;
    private $remote;

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        AbstractChartTermRepositoryInterface $abstractChartTermRepository,
        RemoteInterface $remote
    ) {
        $this->chartRepository = $chartRepository;
        $this->chartTermRepository = $chartTermRepository;
        $this->abstractChartTermRepository = $abstractChartTermRepository;
        $this->remote = $remote;
    }

    public function create(ChartBusinessId $chartBusinessId, ChartTermDate $targetDate = null, \DateInterval $interval = null)
    {
        $abstractChartTerms = [];

        $abstractChartTermSpecification = new AbstractChartTermSpecification();

        $chartEntity = $abstractChartTermSpecification->chartEntity($this->chartRepository, $chartBusinessId);

        $strategyFactory = new StrategyFactory();
        $requestSender = $strategyFactory->createRequestSender($chartEntity);
        $domAnalyzer = $strategyFactory->createDomAnalyzer($chartEntity);
        $adoptionCriteria = $strategyFactory->createAdoptionCriteria($chartEntity);

        $endDateTime = null;
        if (!empty($targetDate)) {
            $endDateTime = new \DateTimeImmutable($targetDate->value());
        }
        $firstEndDateTime = null;
        do {
            $publisherChartTerm = $requestSender->send($this->remote, $endDateTime);
            $domAnalyzer->setDocument($publisherChartTerm);

            $chartStartDatetime = $domAnalyzer->getStartDateTime();
            $chartEndDatetime = $domAnalyzer->getEndDateTime();
            if (empty($chartStartDatetime) || empty($chartEndDatetime)) {
                continue;
            }
            if (empty($firstEndDateTime)) {
                $firstEndDateTime = $chartEndDatetime;
            }
            $abstractChartTerm = new AbstractChartTerm(
                $chartEntity->id(),
                new ChartTermDate($chartStartDatetime->format('Y-m-d')),
                new ChartTermDate($chartEndDatetime->format('Y-m-d'))
            );
            $endDateTime = $domAnalyzer->getNextEndDateTime();
            if ($abstractChartTermSpecification->imported($this->chartTermRepository, $abstractChartTerm->businessId())) {
                continue;
            }

            if ($this->abstractChartTermRepository->exists($abstractChartTerm->businessId())) {
                continue;
            }
            foreach ($domAnalyzer AS $ranking => $row) {
                $abstractChartTerm->addRanking($row['ranking'], $row['chart_artist'], $row['chart_music']);
            }

            if (!$adoptionCriteria->judge($this->abstractChartTermRepository, $abstractChartTerm)) {
                continue;
            }

            if (!$this->abstractChartTermRepository->register($abstractChartTerm)) {
                continue;
            }

            $abstractChartTerms[] = $abstractChartTerm;
        } while ($domAnalyzer->isContinue($firstEndDateTime, $interval));
        return $abstractChartTerms;
    }

}
