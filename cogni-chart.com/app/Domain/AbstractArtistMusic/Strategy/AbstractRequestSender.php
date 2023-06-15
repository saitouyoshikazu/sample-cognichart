<?php

namespace App\Domain\AbstractArtistMusic\Strategy;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicException;
use App\Infrastructure\Remote\RemoteInterface;
use App\Infrastructure\Remote\Scheme;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

abstract class AbstractRequestSender
{

    protected $symbolHandler;
    protected $scheme;
    protected $host;
    protected $uri;

    public function __construct(SymbolHandler $symbolHandler, string $scheme, string $host, string $uri)
    {
        $this->symbolHandler = $symbolHandler;
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

    public function send(RemoteInterface $remote, ChartArtist $chartArtist, ChartMusic $chartMusic)
    {
        return $this->execute($remote, $chartArtist, $chartMusic);
    }

    abstract protected function execute(RemoteInterface $remote, ChartArtist $chartArtist, ChartMusic $chartMusic);

}
