<?php

namespace App\Domain\Artist;
use App\Domain\BusinessIdInterface;
use App\Domain\ValueObjects\ITunesArtistId;

class ArtistBusinessId implements BusinessIdInterface
{

    private $iTunesArtistId;

    public function __construct(ITunesArtistId $iTunesArtistId)
    {
        $this->setITunesArtistId($iTunesArtistId);
    }

    public function setITunesArtistId(ITunesArtistId $iTunesArtistId)
    {
        $this->iTunesArtistId = $iTunesArtistId;
        return $this;
    }

    public function iTunesArtistId()
    {
        return $this->iTunesArtistId;
    }

    public function value()
    {
        return $this->iTunesArtistId->value();
    }

}
