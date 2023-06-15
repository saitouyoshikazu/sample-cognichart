<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\ChartMusic;

trait TraitChartMusic
{

    private $chartMusicValue;

    public function getChartMusic()
    {
        $chartMusicValue = trim($this->chartMusicValue);
        if (empty($chartMusicValue)) {
            return null;
        }
        return new ChartMusic($chartMusicValue);
    }

}
