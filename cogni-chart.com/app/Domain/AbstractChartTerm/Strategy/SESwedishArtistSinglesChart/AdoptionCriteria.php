<?php

namespace App\Domain\AbstractChartTerm\Strategy\SESwedishArtistSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AbstractAdoptionCriteria;
use App\Domain\AbstractChartTerm\AbstractChartTermRepositoryInterface;
use App\Domain\AbstractChartTerm\AbstractChartTerm;

class AdoptionCriteria extends AbstractAdoptionCriteria
{

    public function judge(AbstractChartTermRepositoryInterface $abstractChartTermRepository, AbstractChartTerm $abstractChartTerm)
    {
        $rankings = $abstractChartTerm->rankings();
        if (count($rankings) === 20) {
            return true;
        }
        return false;
    }

}
