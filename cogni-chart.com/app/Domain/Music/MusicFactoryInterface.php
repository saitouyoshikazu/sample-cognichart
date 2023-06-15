<?php

namespace App\Domain\Music;

interface MusicFactoryInterface
{

    /**
     * Create MusicEntity.
     * @param   string  $idValue                    The id of Music.
     * @param   string  $iTunesArtistIdValue        The value of itunes artist_id.
     * @param   string  $musicTitleValue            The title of music.
     * @param   string  $iTunesBaseUrlValue         The base URL of iTunes Affiliate.
     * @param   string  $promotionVideoUrlValue     The value of PromotionVideoUrl.
     * @param   string  $thumbnailUrlValue          The value of ThumbnailUrl.
     * @return  MusicEntity     When MusicEntity was correctly created.
     *          null            When failed to create MusicEntity.
     * @throws  ValueObjectException
     */
    public function create(
        string  $idValue,
        string  $iTunesArtistIdValue,
        string  $musicTitleValue,
        string  $iTunesBaseUrlValue = null,
        string  $promotionVideoUrlValue = null,
        string  $thumbnailUrlValue = null
    );

}
