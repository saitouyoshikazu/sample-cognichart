<?php

namespace App\Domain\AbstractArtistMusic\Strategy;
use App\Domain\AbstractArtistMusic\Strategy\SymbolHandler;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;

abstract class AbstractArtistMusicResolver
{

    protected $symbolHandler;

    public function __construct(SymbolHandler $symbolHandler)
    {
        $this->symbolHandler = $symbolHandler;
    }

    abstract protected function executeResolve(ChartArtist $chartArtist, ChartMusic $chartMusic, array $response): ?Resolved;

    public function resolve(ChartArtist $chartArtist, ChartMusic $chartMusic, array $response)
    {
        $resolved = $this->executeResolve($chartArtist, $chartMusic, $response);
        if (empty($resolved)) {
            return null;
        }
        if (empty($resolved->resolvedArtist()) || empty($resolved->resolvedMusic())) {
            return null;
        }
        return $resolved;
    }

}
