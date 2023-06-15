<?php

namespace App\Application\AbstractChartTerm;
use App\Application\ChartTerm\ChartTermApplicationInterface;
use App\Application\ChartRankingItem\ChartRankingItemApplicationInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermServiceInterface;
use App\Application\DXO\AbstractChartTermDXO;

interface AbstractChartTermApplicationInterface
{

    public function __construct(
        ChartTermApplicationInterface $chartTermApplication,
        ChartRankingItemApplicationInterface $chartRankingItemApplication,
        AbstractChartTermRepositoryInterface $abstractChartTermRepository,
        AbstractChartTermServiceInterface $abstractChartTermService
    );

    public function create(AbstractChartTermDXO $abstractChartTermDXO);

    public function import(AbstractChartTermDXO $abstractChartTermDXO);

}
