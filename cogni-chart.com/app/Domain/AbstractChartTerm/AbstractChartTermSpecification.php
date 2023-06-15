<?php

namespace App\Domain\AbstractChartTerm;
use App\Domain\Chart\ChartRepositoryInterface;
use App\Domain\ChartTerm\ChartTermRepositoryInterface;
use App\Domain\Chart\ChartBusinessId;
use App\Domain\ChartTerm\ChartTermBusinessId;

class AbstractChartTermSpecification
{

    public function chartEntity(ChartRepositoryInterface $chartRepository, ChartBusinessId $chartBusinessId)
    {
        $chartEntity = $chartRepository->getRelease($chartBusinessId);
        if (empty($chartEntity)) {
            $chartEntity = $chartRepository->getProvision($chartBusinessId);
        }
        if (empty($chartEntity)) {
            throw new AbstractChartTermException("Chart doesn't exist. : {$chartBusinessId->value()}");
        }
        return $chartEntity;
    }

    public function imported(ChartTermRepositoryInterface $chartTermRepository, ChartTermBusinessId $chartTermBusinessId)
    {
        $chartTermEntity = $chartTermRepository->getRelease($chartTermBusinessId);
        if (!empty($chartTermEntity)) {
            return true;
        }
        $chartTermEntity = $chartTermRepository->getProvision($chartTermBusinessId);
        if (!empty($chartTermEntity)) {
            return true;
        }
        return false;
    }

}
