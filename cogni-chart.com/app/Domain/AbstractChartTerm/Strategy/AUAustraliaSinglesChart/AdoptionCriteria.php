<?php

namespace App\Domain\AbstractChartTerm\Strategy\AUAustraliaSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractAdoptionCriteria;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTerm;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface;

class AdoptionCriteria extends AbstractAdoptionCriteria
{

    private $chartTermListRepository;
    private $chartTermRepository;
    private $chartRankingItemRepository;

    public function __construct(
        ChartTermListRepositoryInterface $chartTermListRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository
    ) {
        $this->chartTermListRepository = $chartTermListRepository;
        $this->chartTermRepository = $chartTermRepository;
        $this->chartRankingItemRepository = $chartRankingItemRepository;
    }

    protected function rankingCount(AbstractChartTerm $abstractChartTerm)
    {
        $rankings = $abstractChartTerm->rankings();
        if (count($rankings) !== 50) {
            return false;
        }
        return true;
    }

    public function judge(AbstractChartTermRepositoryInterface $abstractChartTermRepository, AbstractChartTerm $abstractChartTerm)
    {
        if (!$this->rankingCount($abstractChartTerm)) {
            return false;
        }

        $chartId = $abstractChartTerm->chartId();
        $releasedLatestChartTermEntity = null;
        $provisionedLatestChartTermEntity = null;
        $latestChartTermAggregation = null;

        $releasedChartTermList = $this->chartTermListRepository->releasedChartTermList($chartId);
        if (!empty($releasedChartTermList)) {
            $releasedLatestChartTermEntity = $releasedChartTermList->nearest($abstractChartTerm->endDate());
        }
        $provisionedChartTermList = $this->chartTermListRepository->provisionedChartTermList($chartId);
        if (!empty($provisionedChartTermList)) {
            $provisionedLatestChartTermEntity = $provisionedChartTermList->nearest($abstractChartTerm->endDate());
        }
        if (empty($releasedLatestChartTermEntity) && empty($provisionedLatestChartTermEntity)) {
            return true;
        } else if (!empty($releasedLatestChartTermEntity) && empty($provisionedLatestChartTermEntity)) {
            $latestChartTermAggregation = $this->chartTermRepository->findAggregationRelease($releasedLatestChartTermEntity->id());
        } else if (empty($releasedLatestChartTermEntity) && !empty($provisionedLatestChartTermEntity)) {
            $latestChartTermAggregation = $this->chartTermRepository->findAggregationProvision($provisionedLatestChartTermEntity->id());
        } else if ($releasedLatestChartTermEntity->endDate()->getDate() >= $provisionedLatestChartTermEntity->endDate()->getDate()) {
            $latestChartTermAggregation = $this->chartTermRepository->findAggregationRelease($releasedLatestChartTermEntity->id());
        } else {
            $latestChartTermAggregation = $this->chartTermRepository->findAggregationProvision($provisionedLatestChartTermEntity->id());
        }

        $always = [];
        foreach ($latestChartTermAggregation->chartRankings() AS $chartRanking) {
            $chartRankingItemEntity = $this->chartRankingItemRepository->find($chartRanking->chartRankingItemId());
            $always[intVal($chartRanking->ranking())] = [
                'ranking'       =>  intVal($chartRanking->ranking()),
                'chart_artist'  =>  strtolower($chartRankingItemEntity->chartArtist()->value()),
                'chart_music'   =>  strtolower($chartRankingItemEntity->chartMusic()->value())
            ];
        }

        $current = [];
        foreach ($abstractChartTerm->rankings() AS $ranking) {
            $current[intVal($ranking['ranking'])] = [
                'ranking'       =>  intVal($ranking['ranking']),
                'chart_artist'  =>  strtolower($ranking['chart_artist']),
                'chart_music'   =>  strtolower($ranking['chart_music'])
            ];
        }

        if ($always == $current) {
            return false;
        }
        return true;
    }

}
