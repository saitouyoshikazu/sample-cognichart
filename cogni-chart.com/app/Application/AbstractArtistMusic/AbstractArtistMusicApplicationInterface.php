<?php

namespace App\Application\AbstractArtistMusic;
use App\Domain\AbstractArtistMusic\AbstractArtistMusicServiceInterface;
use App\Application\DXO\AbstractArtistMusicDXO;

interface AbstractArtistMusicApplicationInterface
{

    public function __construct(AbstractArtistMusicServiceInterface $abstractArtistMuusicService);

    public function prepare(AbstractArtistMusicDXO $abstractArtistMusicDXO);

    public function resolve(AbstractArtistMusicDXO $abstractArtistMusicDXO);

}
