<?php

namespace App\Domain\Artist;

interface ArtistFactoryInterface
{

    /**
     * Create ArtistEntity.
     * @param   string  $idValue                The id of Artist.
     * @param   string  $iTunesArtistIdValue    The value of itunes artist_id.
     * @param   string  $artistNameValue        The name of artist.
     * @return  ArtistEntity    When ArtistEntity was correctly created.
     *          null            When failed to create ArtistEntity.
     * @throws  ValueObjectException
     */
    public function create(
        string  $idValue,
        string  $iTunesArtistIdValue,
        string  $artistNameValue
    );

}
