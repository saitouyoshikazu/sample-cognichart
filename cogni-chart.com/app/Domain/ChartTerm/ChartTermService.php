<?php

namespace domain\chart\chartterm;
use domain\chart\ChartId;
use domain\chart\ChartRepositoryInterface;
use domain\chart\chartterm\ChartTermRepositoryInterface;
use domain\chart\chartterm\ChartTermFactoryInterface;
use domain\chartrankingitem\ChartRankingItemRepositoryInterface;
use domain\chart\ChartSpecification;
use domain\chart\chartterm\ChartTermSpecification;

class ChartTermTakeInService implements ChartTermTakeInServiceInterface
{

    private $chartRepository;
    private $chartTermRepository;
    private $chartTermFactory;
    private $chartRankingItemRepository;

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartTermFactoryInterface $chartTermFactory,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository
    ) {
        $this->chartRepository = $chartRepository;
        $this->chartTermRepository = $chartTermRepository;
        $this->chartTermFactory = $chartTermFactory;
        $this->chartRankingItemRepository = $chartRankingItemRepository;
    }

    public function takeIn(ChartId $chartId, string $startDate, string $endDate, array $rankings, int $recurse = 10, int $interval = 120)
    {
        $chartSpecification = new ChartSpecification();
        $chartTermSpecification = new ChartTermSpecification();

        $chartEntity = $this->chartRepository->created($chartId, $chartSpecification);
        if (empty($chartEntity)) {
            return null;
        }
        $chartTermId = $this->chartTermRepository->createId($chartId, $endDate);
        $chartTermEntity = $this->chartTermRepository->created($chartTermId, $chartTermSpecification);
        if (!empty($chartTermEntity)) {
            return $chartTermEntity;
        }

        $chartRankings = [];
        $rankingsCompleted = true;
        for ($i = 0; $i < $recurse; $i++) {
            sleep($interval);
            $rankingsCompleted = true;
            foreach ($rankings AS $rankig => $rankigItem) {
                if (array_key_exists($ranking, $chartRankings)) {
                    continue;
                }
                $chartArtist = $rankigItem["chart_artist"];
                $chartMusic = $rankigItem["chart_music"];
                $chartRankingItemId = $this->chartRankingItemRepository->createId($chartArtist, $chartMusic);
                $chartRankingItemEntity = $this->chartRankingItemRepository->find($chartRankingItemId);
                if (empty($chartRankingItemEntity)) {
                    $rankingsCompleted = false;
                    break;
                }
                $chartRanking = $chartTermFactory->createChartRanking($ranking, $chartRankingItemId->getId()->getId());
                $chartRankings[$ranking] = $chartRanking;
            }
            if ($rankingsCompleted === true) {
                break;
            }
        }
        if ($rankingsCompleted === false) {
            return null;
        }

        $chartTermEntity = $chartTermFactory->createChartTermEntity(
            $chartTermId,
            $chartId,
            $startDate,
            $endDate
        );
        foreach ($chartRankings AS $ranking => $chartRanking) {
            $chartTermEntity->addChartRanking($chartRanking);
        }
        return $chartTermEntity;
    }

}

