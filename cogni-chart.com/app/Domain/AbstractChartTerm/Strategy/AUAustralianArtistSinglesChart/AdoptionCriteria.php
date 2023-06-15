<?php

namespace App\Domain\AbstractChartTerm\Strategy\AUAustralianArtistSinglesChart;
use App\Domain\AbstractChartTerm\Strategy\AUAustraliaSinglesChart\AdoptionCriteria AS AUAustraliaSinglesChartAdoptionCriteria;
use App\Domain\AbstractChartTerm\AbstractChartTerm;

class AdoptionCriteria extends AUAustraliaSinglesChartAdoptionCriteria
{

    protected function rankingCount(AbstractChartTerm $abstractChartTerm)
    {
        $rankings = $abstractChartTerm->rankings();
        if (count($rankings) !== 20) {
            return false;
        }
        return true;
    }

}
