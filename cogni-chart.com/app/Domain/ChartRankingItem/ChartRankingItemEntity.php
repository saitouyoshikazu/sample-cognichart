<?php

namespace App\Domain\ChartRankingItem;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

class ChartRankingItemEntity extends Entity
{

    private $chartArtist;
    private $chartMusic;
    private $artistId;
    private $musicId;

    public function __construct(EntityId $id, ChartArtist $chartArtist, ChartMusic $chartMusic)
    {
        parent::__construct($id);
        $this->setChartArtist($chartArtist)->setChartMusic($chartMusic);
    }

    protected function setBusinessId()
    {
        if (empty($this->chartArtist) || empty($this->chartMusic)) {
            $this->businessId = null;
            return;
        }
        if (empty($this->businessId)) {
            $this->businessId = new ChartRankingItemBusinessId($this->chartArtist, $this->chartMusic);
            return;
        }
        $this->businessId->setChartArtist($this->chartArtist)->setChartMusic($this->chartMusic);
    }

    public function setChartArtist(ChartArtist $chartArtist)
    {
        $this->chartArtist = $chartArtist;
        $this->setBusinessId();
        return $this;
    }

    public function chartArtist()
    {
        return $this->chartArtist;
    }

    public function setChartMusic(ChartMusic $chartMusic)
    {
        $this->chartMusic = $chartMusic;
        $this->setBusinessId();
        return $this;
    }

    public function chartMusic()
    {
        return $this->chartMusic;
    }

    public function setArtistId(EntityId $artistId = null)
    {
        $this->artistId = $artistId;
        return $this;
    }

    public function artistId()
    {
        if (empty($this->artistId)) {
            return '';
        }
        return $this->artistId;
    }

    public function setMusicId(EntityId $musicId = null)
    {
        $this->musicId = $musicId;
        return $this;
    }

    public function musicId()
    {
        if (empty($this->musicId)) {
            return '';
        }
        return $this->musicId;
    }

    public function isResolved()
    {
        if (empty($this->artistId) && empty($this->musicId)) {
            return false;
        }
        return true;
    }

}
