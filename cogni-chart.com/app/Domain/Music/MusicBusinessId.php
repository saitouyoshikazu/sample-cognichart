<?php

namespace App\Domain\Music;
use App\Domain\BusinessIdInterface;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;

class MusicBusinessId implements BusinessIdInterface
{

    private $iTunesArtistId;
    private $musicTitle;

    public function __construct(ITunesArtistId $iTunesArtistId, MusicTitle $musicTitle)
    {
        $this
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle);
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

    public function setMusicTitle(MusicTitle $musicTitle)
    {
        $this->musicTitle = $musicTitle;
        return $this;
    }

    public function musicTitle()
    {
        return $this->musicTitle;
    }

    public function value()
    {
        return $this->iTunesArtistId->value()."-".$this->musicTitle->value();
    }

}
