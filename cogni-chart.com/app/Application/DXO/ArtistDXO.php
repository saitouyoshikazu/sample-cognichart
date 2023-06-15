<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitPhase;
use App\Application\DXO\Traits\TraitEntityId;
use App\Application\DXO\Traits\TraitITunesArtistId;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\Artist\ArtistBusinessId;

class ArtistDXO
{

    use TraitPhase, TraitEntityId, TraitITunesArtistId;

    private $artistNameValue;

    public function find(string $phaseValue, string $entityIdValue)
    {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
    }

    public function get(string $phaseValue, string $iTunesArtistIdValue)
    {
        $this->phaseValue = $phaseValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
    }

    public function register(string $iTunesArtistIdValue, string $artistNameValue)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function modify(string $phaseValue, string $entityIdValue, string $iTunesArtistIdValue, string $artistNameValue)
    {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function delete(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function release(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function rollback(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function refreshCachedEntity(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function provisionedEntities(string $iTunesArtistIdValue = null, string $artistNameValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function releasedEntities(string $iTunesArtistIdValue = null, string $artistNameValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function provisionedPaginator(string $iTunesArtistIdValue = null, string $artistNameValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function releasedPaginator(string $iTunesArtistIdValue = null, string $artistNameValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->artistNameValue = $artistNameValue;
    }

    public function getArtistName()
    {
        $artistNameValue = trim($this->artistNameValue);
        if (empty($artistNameValue)) {
            return null;
        }
        return new ArtistName($artistNameValue);
    }

    public function getBusinessId()
    {
        $iTunesArtistId = $this->getITunesArtistId();
        if (empty($iTunesArtistId)) {
            return null;
        }
        return new ArtistBusinessId($iTunesArtistId);
    }

}
