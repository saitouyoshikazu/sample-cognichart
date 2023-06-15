<?php

namespace App\Infrastructure\Repositories;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ChartTerm\ChartTermFactoryInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\Phase;
use App\Domain\ChartTerm\ChartTermList;
use App\Infrastructure\Eloquents\ChartTerm;
use App\Infrastructure\Eloquents\ProvisionedChartTerm;

class ChartTermListRepository implements ChartTermListRepositoryInterface
{

    private $chartTermFactory;

    public function __construct(ChartTermFactoryInterface $chartTermFactory)
    {
        $this->chartTermFactory = $chartTermFactory;
    }

    public function chartTermList(EntityId $chartId, Phase $phase)
    {
        if ($phase->value() === Phase::provisioned) {
            return $this->provisionedChartTermList($chartId);
        }
        return $this->releasedChartTermList($chartId);
    }

    public function releasedChartTermList(EntityId $chartId)
    {
        $rows = ChartTerm::where('chart_id', $chartId->value())->orderBy('end_date', 'desc')->get();
        if (empty($rows)) {
            return null;
        }
        $chartTermList = null;
        foreach ($rows AS $row) {
            $chartTermEntity = $this->chartTermFactory->create(
                $row->id,
                $row->chart_id,
                $row->start_date,
                $row->end_date
            );
            if (!empty($chartTermEntity)) {
                if (empty($chartTermList)) {
                    $chartTermList = new ChartTermList(new Phase(Phase::released));
                }
                $chartTermList->append($chartTermEntity);
            }
        }
        return $chartTermList;
    }

    public function provisionedChartTermList(EntityId $chartId)
    {
        $rows = ProvisionedChartTerm::where('chart_id', $chartId->value())->orderBy('end_date', 'desc')->get();
        if (empty($rows)) {
            return null;
        }
        $chartTermList = null;
        foreach ($rows AS $row) {
            $chartTermEntity = $this->chartTermFactory->create(
                $row->id,
                $row->chart_id,
                $row->start_date,
                $row->end_date
            );
            if (!empty($chartTermEntity)) {
                if (empty($chartTermList)) {
                    $chartTermList = new ChartTermList(new Phase(Phase::provisioned));
                }
                $chartTermList->append($chartTermEntity);
            }
        }
        return $chartTermList;
    }

}
