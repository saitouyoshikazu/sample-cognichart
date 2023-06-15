<?php

namespace App\Domain\ChartRankingItem;
use App\Domain\BusinessIdInterface;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

class ChartRankingItemBusinessId implements BusinessIdInterface
{

    private $chartArtist;
    private $chartMusic;

    public function __construct(ChartArtist $chartArtist, ChartMusic $chartMusic)
    {
        $this->setChartArtist($chartArtist)->setChartMusic($chartMusic);
    }

    public function setChartArtist(ChartArtist $chartArtist)
    {
        $this->chartArtist = $chartArtist;
        return $this;
    }

    public function chartArtist()
    {
        return $this->chartArtist;
    }

    public function setChartMusic(ChartMusic $chartMusic)
    {
        $this->chartMusic = $chartMusic;
        return $this;
    }

    public function chartMusic()
    {
        return $this->chartMusic;
    }

    public function value()
    {
        return $this->chartArtist()->value() . '-' . $this->chartMusic()->value();
    }

}
