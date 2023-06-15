<?php

namespace App\Domain\Artist;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;

class ArtistEntity extends Entity
{

    private $iTunesArtistId;
    private $artistName;

    public function __construct(EntityId $id, ITunesArtistId $iTunesArtistId)
    {
        parent::__construct($id);
        $this->setITunesArtistId($iTunesArtistId);
    }

    public function setITunesArtistId(ITunesArtistId $iTunesArtistId)
    {
        $this->iTunesArtistId = $iTunesArtistId;
        $this->setBusinessId();
        return $this;
    }

    public function iTunesArtistId()
    {
        return $this->iTunesArtistId;
    }

    protected function setBusinessId()
    {
        if (empty($this->iTunesArtistId)) {
            $this->businessId = null;
            return;
        }
        if (empty($this->businessId)) {
            $this->businessId = new ArtistBusinessId($this->iTunesArtistId);
            return;
        }
        $this->businessId
            ->setITunesArtistId($this->iTunesArtistId);
    }

    public function setArtistName(ArtistName $artistName)
    {
        $this->artistName = $artistName;
        return $this;
    }

    public function artistName()
    {
        return $this->artistName;
    }

}
