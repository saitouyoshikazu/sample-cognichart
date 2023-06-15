<?php

namespace App\Application\AbstractChartTerm;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface;
use Event;
use App\Application\DXO\AbstractChartTermDXO;
use App\Application\DXO\ChartRankingItemDXO;
use App\Application\DXO\ChartTermDXO;
use App\Domain\AbstractChartTerm\AbstractChartTermException;
use App\Events\AbstractChartTermCreated;

class AbstractChartTermApplication implements AbstractChartTermApplicationInterface
{

    private $chartTermApplication;
    private $chartRankingItemApplication;
    private $abstractChartTermRepository;
    private $abstractChartTermService;

    public function __construct(
        ChartTermApplicationInterface $chartTermApplication,
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        AbstractChartTermRepositoryInterface $abstractChartTermRepository,
        AbstractChartTermServiceInterface $abstractChartTermService
    ) {
        $this->chartTermApplication = $chartTermApplication;
        $this->chartRankingItemApplication = $chartRankingItemApplication;
        $this->abstractChartTermRepository = $abstractChartTermRepository;
        $this->abstractChartTermService = $abstractChartTermService;
    }

    public function create(AbstractChartTermDXO $abstractChartTermDXO)
    {
        $chartBusinessId = $abstractChartTermDXO->getChartBusinessId();
        if (empty($chartBusinessId)) {
            return false;
        }
        $targetDate = $abstractChartTermDXO->getTargetDate();
        $interval = $abstractChartTermDXO->getInterval();

        $abstractChartTerms = $this->abstractChartTermService->create($chartBusinessId, $targetDate, $interval);
        if (empty($abstractChartTerms)) {
            return true;
        }
        foreach ($abstractChartTerms AS $abstractChartTerm) {
            Event::dispatch(
                new AbstractChartTermCreated(
                    $abstractChartTerm->businessId()->chartId()->value(),
                    $abstractChartTerm->businessId()->endDate()->value()
                )
            );
        }
        return true;
    }

    public function import(AbstractChartTermDXO $abstractChartTermDXO)
    {
        $chartTermBusinessId = $abstractChartTermDXO->getChartTermBusinessId();
        if (empty($chartTermBusinessId)) {
            return false;
        }

        if (!$this->abstractChartTermRepository->exists($chartTermBusinessId)) {
            return false;
        }
        $abstractChartTerm = $this->abstractChartTermRepository->get($chartTermBusinessId);

        $rankings = $abstractChartTerm->rankings();
        if (empty($rankings)) {
            return false;
        }

        $completed = false;
        $retryCount = 0;
        while ($retryCount < 3 && $completed === false) {
            $completed = true;
            foreach ($rankings AS $ranking) {
                $chartRankingItemDXO = new ChartRankingItemDXO();
                $chartRankingItemDXO->exists($ranking['chart_artist'], $ranking['chart_music']);
                if ($this->chartRankingItemApplication->exists($chartRankingItemDXO)) {
                    $retryCount = 0;
                    continue;
                }
                try {
                    $chartRankingItemDXO = new ChartRankingItemDXO();
                    $chartRankingItemDXO->register($ranking['chart_artist'], $ranking['chart_music']);
                    $result = $this->chartRankingItemApplication->register($chartRankingItemDXO);
                    if ($result === false) {
                        $completed = false;
                        $retryCount++;
                        break;
                    }
                    $retryCount = 0;
                } catch(\Exception $e) {
                    if ($e->getMessage() === "Couldn't register ChartRankingItemEntity because ChartRankingItem is already existing.") {
                        $completed = false;
                        $retryCount++;
                        break;
                    } else {
                        throw $e;
                    }
                }
                set_time_limit(ini_get('max_execution_time'));
                sleep(10);
            }
        }
        if ($retryCount >= 3) {
            throw new AbstractChartTermException("Failed to import ChartRankingItems.");
        }

        $chartTermDXO = new ChartTermDXO();
        $chartTermDXO->register($abstractChartTerm->chartId()->value(), $abstractChartTerm->startDate()->value(), $abstractChartTerm->endDate()->value());
        $rankings = $abstractChartTerm->rankings();
        foreach ($rankings AS $ranking) {
            $chartRankingItemDXO = new ChartRankingItemDXO();
            $chartRankingItemDXO->get($ranking['chart_artist'], $ranking['chart_music']);
            $chartRankingItemEntity = $this->chartRankingItemApplication->get($chartRankingItemDXO);
            $chartTermDXO->addRanking($ranking['ranking'], $chartRankingItemEntity->id()->value());
        }
        $result = $this->chartTermApplication->register($chartTermDXO);
        if ($result === false) {
            throw new AbstractChartTermException("Failed to import ChartTerm.");
        }
        return true;
    }

}
