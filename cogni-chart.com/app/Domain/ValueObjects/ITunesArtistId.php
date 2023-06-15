<?php

namespace App\Domain\ValueObjects;

class ITunesArtistId
{

    private $iTunesArtistId;

    public function __construct(string $iTunesArtistId)
    {
        $iTunesArtistId = trim($iTunesArtistId);
        if (empty($iTunesArtistId)) {
            throw new ValueObjectException("Can't set empty value in ITunesArtistId.");
        }
        $this->iTunesArtistId = $iTunesArtistId;
    }

    public function value()
    {
        return $this->iTunesArtistId;
    }

}
