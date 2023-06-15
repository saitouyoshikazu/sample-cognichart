<?php

namespace App\Application\Music;
use App\Domain\Music\MusicRepositoryInterface;
use App\Domain\Music\MusicFactoryInterface;
use App\Domain\Music\MusicServiceInterface;
use App\Application\DXO\MusicDXO;

interface MusicApplicationInterface
{

    public function __construct(
        MusicRepositoryInterface $musicRepository,
        MusicFactoryInterface $musicFactory,
        MusicServiceInterface $musicService
    );

    public function find(MusicDXO $musicDXO);

    public function get(MusicDXO $musicDXO);

    public function register(MusicDXO $musicDXO);

    public function modify(MusicDXO $musicDXO);

    public function delete(MusicDXO $musicDXO);

    public function release(MusicDXO $musicDXO);

    public function rollback(MusicDXO $musicDXO);

    public function refreshCachedEntity(MusicDXO $musicDXO);

    public function checkPromotionVideo(MusicDXO $musicDXO);

    public function promotionVideoBrokenLinks(MusicDXO $musicDXO);

    public function deletePromotionVideoBrokenLink(MusicDXO $musicDXO);

    public function deleteWithITunesArtistId(MusicDXO $musicDXO);

    public function replaceITunesArtistId(MusicDXO $musicDXO);

    public function provisionedPaginator(MusicDXO $musicDXO);

    public function releasedPaginator(MusicDXO $musicDXO);

}
