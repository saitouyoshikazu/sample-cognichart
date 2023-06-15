<?php

namespace domain\chart\chartterm;
use domain\chart\ChartId;
use domain\chart\ChartRepositoryInterface;
use domain\chart\chartterm\ChartTermRepositoryInterface;
use domain\chart\chartterm\ChartTermFactoryInterface;
use domain\chartrankingitem\ChartRankingItemRepositoryInterface;
use domain\chart\ChartSpecification;
use domain\chart\chartterm\ChartTermSpecification;

interface ChartTermTakeInServiceInterface
{

    /**
     * The constructor.
     *
     * @param   ChartRepositoryInterface            $chartRepository            ChartRepository.
     * @param   ChartTermRepositoryInterface        $chartTermRepository        ChartTermRepository.
     * @param   ChartTermFactoryInterface           $chartTermFactory           ChartTermFactory.
     * @param   ChartRankingItemRepositoryInterface $chartRankingItemRepository ChartRankingItemRepository.
     */
    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        ChartTermFactoryInterface $chartTermFactory,
        ChartRankingItemRepositoryInterface $chartRankingItemRepository
    );

    /**
     * Take in rankings of ChartTerm.
     *
     * @param   ChartId $chartId    The id of chart.
     * @param   string  $startDate  Starting date of term of chart.
     * @param   string  $endDate    Ending date of term of chart.
     * @param   array   $rankings   Rankings of term of chart.
     * @param   int     $recurse    Recursive count of checking that ranking was created.
     * @param   int     $interval   Interval seconds of checking that ranking was created.
     *
     * @return  ChartTermEntity
     *              Chart is existing and ChartTerm isn't existing and all rankings was created.
     *          null
     *              Chart doesn't exist or ChartTerm is already existing or some of rankings wasn't created.
     */
    public function takeIn(ChartId $chartId, string $startDate, string $endDate, array $rankings, int $recurse = 10, int $interval = 120);

}

