<?php

namespace App\Domain\AbstractArtistMusic\Strategy;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicException;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;

abstract class AbstractArtistClarifying
{

    protected $remote;
    protected $scheme;
    protected $host;
    protected $uri;

    public function __construct(
        RemoteInterface $remote,
        string $scheme,
        string $host,
        string $uri = null
    ) {
        $this->remote = $remote;
        $this->scheme = new Scheme($scheme);
        $host = trim($host);
        if (empty($host)) {
            throw new AbstractArtistMusicException("Can't set empty value in host.");
        }
        $this->host = $host;
        $uri = trim($uri);
        if (empty($uri)) {
            $uri = "";
        }
        $this->uri = $uri;
    }

    abstract public function clarify(string $itunesArtistIdValue);

}
