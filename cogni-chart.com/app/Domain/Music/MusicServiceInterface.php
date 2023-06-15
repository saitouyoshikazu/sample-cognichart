<?php

namespace App\Domain\Music;
use App\Infrastructure\Remote\RemoteInterface;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\Music\MusicEntity;

interface MusicServiceInterface
{

    /**
     * Constructor.
     * @param   RemoteInterface     $remote     Remote.
     */
    public function __construct(RemoteInterface $remote);

    /**
     * Search information of PromotionVideo to be inferred from ArtistName and MusicTitle.
     * @param   ArtistName  $artistName     The name of Artist.
     * @param   MusicTitle  $musicTitle     The title of Music.
     * @return  Array   When information of PromotionVideo was found.
     *                  Array includes url of PromotionVideo and thumbnail url of PromotionVideo.
     *          null    When information of PromotionVideo couldn't find.
     */
    public function searchPromotionVideo(ArtistName $artistName, MusicTitle $musicTitle);

    /**
     * Check if url of PromotionVideo is broken.
     * @param   MusicEntity     MusicEntity     MusicEntity.
     * @return  bool    When url of PromotionVideo is available, return true.
     *                  When url of PromotionVideo is not available, return false.
     */
    public function checkPromotionVideo(MusicEntity $musicEntity);

}
