<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitEntityId;
use App\Application\DXO\Traits\TraitChartArtist;
use App\Application\DXO\Traits\TraitChartMusic;

class AbstractArtistMusicDXO
{

    use TraitEntityId, TraitChartArtist, TraitChartMusic;

    private $chartRankingItemIdValue;
    private $chartArtistValue;
    private $chartMusicValue;

    public function prepare(string $entityIdValue, string $chartArtistValue, string $chartMusicValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function resolve(string $entityIdValue, string $chartArtistValue, string $chartMusicValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

}
