<?php

namespace App\Domain\AbstractArtistMusic\Strategy;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicException;

class Resolved
{

    private $resolvedArtistValue;
    private $resolvedMusicValue;
    private $resolvedArtistIdValue;
    private $resolvedITunesBaseUrl;

    public function __construct(
        string $resolvedArtistValue,
        string $resolvedMusicValue,
        string $resolvedArtistIdValue = null,
        string $resolvedITunesBaseUrl = null
    ) {
        $this
            ->setResolvedArtist($resolvedArtistValue)
            ->setResolvedMusic($resolvedMusicValue)
            ->setResolvedArtistId($resolvedArtistIdValue)
            ->setResolvedITunesBaseUrl($resolvedITunesBaseUrl);
    }

    public function setResolvedArtist(string $resolvedArtistValue)
    {
        $resolvedArtistValue = trim($resolvedArtistValue);
        if (empty($resolvedArtistValue)) {
            throw new AbstractArtistMusicException("Can't set empty value at resolvedArtist.");
        }
        $this->resolvedArtistValue = $resolvedArtistValue;
        return $this;
    }

    public function setResolvedMusic(string $resolvedMusicValue)
    {
        $resolvedMusicValue = trim($resolvedMusicValue);
        if (empty($resolvedMusicValue)) {
            throw new AbstractArtistMusicException("Can't set empty value at resolvedMusic.");
        }
        $this->resolvedMusicValue = $resolvedMusicValue;
        return $this;
    }

    private function setResolvedArtistId(string $resolvedArtistIdValue = null)
    {
        $resolvedArtistIdValue = trim($resolvedArtistIdValue);
        $this->resolvedArtistIdValue = $resolvedArtistIdValue;
        return $this;
    }

    private function setResolvedITunesBaseUrl(string $resolvedITunesBaseUrl = null)
    {
        $resolvedITunesBaseUrl = trim($resolvedITunesBaseUrl);
        $this->resolvedITunesBaseUrl = $resolvedITunesBaseUrl;
        return $this;
    }

    public function resolvedArtist()
    {
        return $this->resolvedArtistValue;
    }

    public function resolvedMusic()
    {
        return $this->resolvedMusicValue;
    }

    public function resolvedArtistId()
    {
        return $this->resolvedArtistIdValue;
    }

    public function resolvedITunesBaseUrl()
    {
        return $this->resolvedITunesBaseUrl;
    }

}
