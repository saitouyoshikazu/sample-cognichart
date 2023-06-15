<?php

namespace App\Domain\AbstractChartTerm;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\ValueObjects\ChartTermDate;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\ChartTerm\ChartTermBusinessId;

interface AbstractChartTermServiceInterface
{

    public function __construct(
        ChartRepositoryInterface $chartRepository,
        ChartTermRepositoryInterface $chartTermRepository,
        AbstractChartTermRepositoryInterface $abstractChartTermRepository,
        RemoteInterface $remote
    );

    /**
     * Create AbstractChartTerm.
     */
    public function create(ChartBusinessId $chartBusinessId, ChartTermDate $targetDate, \DateInterval $interval = null);

}
