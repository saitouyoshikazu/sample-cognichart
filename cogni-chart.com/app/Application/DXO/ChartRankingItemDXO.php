<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitEntityId;
use App\Application\DXO\Traits\TraitChartArtist;
use App\Application\DXO\Traits\TraitChartMusic;
use App\Domain\EntityId;
use App\Domain\ChartRankingItem\ChartRankingItemBusinessId;

class ChartRankingItemDXO
{

    use TraitEntityId, TraitChartArtist, TraitChartMusic;

    private $artistIdValue;
    private $musicIdValue;

    public function exists(string $chartArtistValue, string $chartMusicValue)
    {
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function find(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function get(string $chartArtistValue, string $chartMusicValue)
    {
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function register(string $chartArtistValue, string $chartMusicValue, string $artistIdValue = null, string $musicIdValue = null)
    {
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
        $this->artistIdValue = $artistIdValue;
        $this->musicIdValue = $musicIdValue;
    }

    public function modify(string $entityIdValue, string $chartArtistValue, string $chartMusicValue, string $artistIdValue = null, string $musicIdValue = null)
    {
        $this->entityIdValue = $entityIdValue;
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
        $this->artistIdValue = $artistIdValue;
        $this->musicIdValue = $musicIdValue;
    }

    public function refreshCachedEntity(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function detachArtist(string $artistIdValue)
    {
        $this->artistIdValue = $artistIdValue;
    }

    public function detachMusic(string $musicIdValue)
    {
        $this->musicIdValue = $musicIdValue;
    }

    public function notAttachedPaginator(string $chartArtistValue = null, string $chartMusicValue = null)
    {
        $this->chartArtistValue = $chartArtistValue;
        $this->chartMusicValue = $chartMusicValue;
    }

    public function getArtistId()
    {
        $artistIdValue = trim($this->artistIdValue);
        if (empty($artistIdValue)) {
            return null;
        }
        return new EntityId($artistIdValue);
    }

    public function getMusicId()
    {
        $musicIdValue = trim($this->musicIdValue);
        if (empty($musicIdValue)) {
            return null;
        }
        return new EntityId($musicIdValue);
    }

    public function getBusinessId()
    {
        
        $chartArtist = $this->getChartArtist();
        $chartMusic = $this->getChartMusic();
        if (empty($chartArtist) || empty($chartMusic)) {
            return null;
        }
        return new ChartRankingItemBusinessId($chartArtist, $chartMusic);
    }

}
