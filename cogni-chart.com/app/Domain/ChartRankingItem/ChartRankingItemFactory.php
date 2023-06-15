<?php

namespace App\Domain\ChartRankingItem;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

class ChartRankingItemFactory implements ChartRankingItemFactoryInterface
{

    public function create(
        string  $idValue,
        string  $chartArtistValue,
        string  $chartMusicValue,
        string  $artistIdValue = null,
        string  $musicIdValue  = null
    ) {
        $idValue = trim($idValue);
        if (empty($idValue)) {
            return null;
        }
        $id = new EntityId($idValue);
        $chartArtist = new ChartArtist($chartArtistValue);
        $chartMusic = new ChartMusic($chartMusicValue);
        $chartRankingItemEntity = new ChartRankingItemEntity($id, $chartArtist, $chartMusic);
        $artistIdValue = trim($artistIdValue);
        $musicIdValue = trim($musicIdValue);
        if (!empty($artistIdValue)) {
            $artistId = new EntityId($artistIdValue);
            $chartRankingItemEntity->setArtistId($artistId);
        }
        if (!empty($musicIdValue)) {
            $musicId = new EntityId($musicIdValue);
            $chartRankingItemEntity->setMusicId($musicId);
        }
        return $chartRankingItemEntity;
    }

}

