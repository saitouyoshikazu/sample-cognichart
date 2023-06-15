<?php

namespace App\Domain\Artist;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;

class ArtistFactory implements ArtistFactoryInterface
{

    public function create(
        string  $idValue,
        string  $iTunesArtistIdValue,
        string  $artistNameValue
    ) {
        $entityId = new EntityId($idValue);
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $artistName = new ArtistName($artistNameValue);
        $artistEntity = new ArtistEntity($entityId, $iTunesArtistId);
        $artistEntity
            ->setArtistName($artistName);
        return $artistEntity;
    }

}
