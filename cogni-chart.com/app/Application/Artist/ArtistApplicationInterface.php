<?php

namespace App\Application\Artist;
use App\Domain\Artist\ArtistRepositoryInterface;
use App\Domain\Artist\ArtistFactoryInterface;
use App\Application\DXO\ArtistDXO;

interface ArtistApplicationInterface
{

    public function __construct(
        ArtistRepositoryInterface $artistRepository,
        ArtistFactoryInterface $artistFactory
    );

    public function find(ArtistDXO $artistDXO);

    public function get(ArtistDXO $artistDXO);

    public function register(ArtistDXO $artistDXO);

    public function modify(ArtistDXO $artistDXO);

    public function delete(ArtistDXO $artistDXO);

    public function release(ArtistDXO $artistDXO);

    public function rollback(ArtistDXO $artistDXO);

    public function refreshCachedEntity(ArtistDXO $artistDXO);

    public function provisionedEntities(ArtistDXO $artistDXO);

    public function releasedEntities(ArtistDXO $artistDXO);

    public function provisionedPaginator(ArtistDXO $artistDXO);

    public function releasedPaginator(ArtistDXO $artistDXO);

}
