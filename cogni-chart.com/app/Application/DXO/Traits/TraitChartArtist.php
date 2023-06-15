<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\ChartArtist;

trait TraitChartArtist
{

    private $chartArtistValue;

    public function getChartArtist()
    {
        $chartArtistValue = trim($this->chartArtistValue);
        if (empty($chartArtistValue)) {
            return new ChartArtist('');
        }
        return new ChartArtist($chartArtistValue);
    }

}
