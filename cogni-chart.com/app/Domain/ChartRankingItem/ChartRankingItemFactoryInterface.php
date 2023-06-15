<?php

namespace App\Domain\ChartRankingItem;

interface ChartRankingItemFactoryInterface
{

    /**
     * Create ChartRankingItemEntity.
     * @param   string  $idValue            The id of ChartRankingItem.
     * @param   string  $chartArtistValue   The name of artist is posted by publisher.
     * @param   string  $chartMusicValue    The name of music is posted by publisher.
     * @param   string  $artistIdValue      The id of Artist.
     * @param   string  $musicIdValue       The id of Music.
     * @return  ChartRankingItemEntity  When ChartRankingItemEntity was correctly created.
     *          null                    When failed to create ChartRankingItemEntity.
     * @throws  ValueObjectException
     */
    public function create(
        string  $idValue,
        string  $chartArtistValue,
        string  $chartMusicValue,
        string  $artistIdValue = null,
        string  $musicIdValue  = null
    );

}
