<?php

namespace App\Domain\Music;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\ITunesBaseUrl;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;

class MusicFactory implements MusicFactoryInterface
{

    public function create(
        string  $idValue,
        string  $iTunesArtistIdValue,
        string  $musicTitleValue,
        string  $iTunesBaseUrlValue = null,
        string  $promotionVideoUrlValue = null,
        string  $thumbnailUrlValue = null
    ) {
        $entityId = new EntityId($idValue);
        $iTunesArtistId = new ITunesArtistId($iTunesArtistIdValue);
        $musicTitle = new MusicTitle($musicTitleValue);
        $musicEntity = new MusicEntity($entityId, $iTunesArtistId, $musicTitle);
        $iTunesBaseUrlValue = trim($iTunesBaseUrlValue);
        if (!empty($iTunesBaseUrlValue)) {
            $iTunesBaseUrl = new ITunesBaseUrl($iTunesBaseUrlValue);
            $musicEntity->setITunesBaseUrl($iTunesBaseUrl);
        }
        $promotionVideoUrlValue = trim($promotionVideoUrlValue);
        if (!empty($promotionVideoUrlValue)) {
            $promotionVideoUrl = new PromotionVideoUrl($promotionVideoUrlValue);
            $musicEntity->setPromotionVideoUrl($promotionVideoUrl);
        }
        $thumbnailUrlValue = trim($thumbnailUrlValue);
        if (!empty($thumbnailUrlValue)) {
            $thumbnailUrl = new ThumbnailUrl($thumbnailUrlValue);
            $musicEntity->setThumbnailUrl($thumbnailUrl);
        }
        return $musicEntity;
    }

}
