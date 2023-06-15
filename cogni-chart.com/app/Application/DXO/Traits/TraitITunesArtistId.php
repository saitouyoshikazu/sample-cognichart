<?php

namespace App\Application\DXO\Traits;
use App\Domain\ValueObjects\ITunesArtistId;

trait TraitITunesArtistId
{

    private $iTunesArtistIdValue;

    public function getITunesArtistId()
    {
        $iTunesArtistIdValue = trim($this->iTunesArtistIdValue);
        if (empty($iTunesArtistIdValue)) {
            return null;
        }
        return new ITunesArtistId($iTunesArtistIdValue);
    }

}
