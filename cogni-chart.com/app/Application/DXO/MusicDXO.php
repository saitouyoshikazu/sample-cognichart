<?php

namespace App\Application\DXO;
use App\Application\DXO\Traits\TraitPhase;
use App\Application\DXO\Traits\TraitEntityId;
use App\Application\DXO\Traits\TraitITunesArtistId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\ITunesBaseUrl;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;
use App\Domain\ValueObjects\CheckPromotionVideoConditions;
use App\Domain\Music\MusicBusinessId;

class MusicDXO
{

    use TraitPhase, TraitEntityId, TraitITunesArtistId;

    private $artistNameValue;
    private $musicTitleValue;
    private $iTunesBaseUrlValue;
    private $promotionVideoUrlValue;
    private $thumbnailUrlValue;
    private $checkPromotionVideoConditions;
    private $iTunesArtistIds;
    private $oldITunesArtistIdValue;

    public function find(string $phaseValue, string $entityIdValue)
    {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
    }

    public function get(string $phaseValue, string $iTunesArtistIdValue, string $musicTitleValue)
    {
        $this->phaseValue = $phaseValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->musicTitleValue = $musicTitleValue;
    }

    public function register(
        string $iTunesArtistIdValue,
        string $musicTitleValue,
        string $iTunesBaseUrlValue = null,
        string $promotionVideoUrlValue = null,
        string $thumbnailUrlValue = null
    ) {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->musicTitleValue = $musicTitleValue;
        $this->iTunesBaseUrlValue = $iTunesBaseUrlValue;
        $this->promotionVideoUrlValue = $promotionVideoUrlValue;
        $this->thumbnailUrlValue = $thumbnailUrlValue;
    }

    public function modify(
        string $phaseValue,
        string $entityIdValue,
        string $iTunesArtistIdValue,
        string $musicTitleValue,
        string $iTunesBaseUrlValue = null,
        string $promotionVideoUrlValue = null,
        string $thumbnailUrlValue = null
    ) {
        $this->phaseValue = $phaseValue;
        $this->entityIdValue = $entityIdValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->musicTitleValue = $musicTitleValue;
        $this->iTunesBaseUrlValue = $iTunesBaseUrlValue;
        $this->promotionVideoUrlValue = $promotionVideoUrlValue;
        $this->thumbnailUrlValue = $thumbnailUrlValue;
    }

    public function delete(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function release(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function rollback(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function refreshCachedEntity(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function assignPromotionVideo(string $entityIdValue, string $artistNameValue, string $musicTitleValue)
    {
        $this->entityIdValue = $entityIdValue;
        $this->artistNameValue = $artistNameValue;
        $this->musicTitleValue = $musicTitleValue;
    }

    public function checkPromotionVideoAppendCreatedAtGTE(string $createdAtValue)
    {
        $this->createCheckPromotionVideoConditions();
        $this->checkPromotionVideoConditions->appendCreatedAtGTE($createdAtValue);
    }

    public function checkPromotionVideoAppendCreatedAtLT(string $createdAtValue)
    {
        $this->createCheckPromotionVideoConditions();
        $this->checkPromotionVideoConditions->appendCreatedAtLT($createdAtValue);
    }

    public function checkPromotionVideoAppendMusicIdLike(string $musicIdLikeValue)
    {
        $this->createCheckPromotionVideoConditions();
        $this->checkPromotionVideoConditions->appendMusicIdLike($musicIdLikeValue);
    }

    public function promotionVideoBrokenLinksAppendItunesArtistId(string $iTunesArtistIdValue)
    {
        $this->iTunesArtistIds[] = new ITunesArtistId($iTunesArtistIdValue);
    }

    public function deletePromotionVideoBrokenLink(string $entityIdValue)
    {
        $this->entityIdValue = $entityIdValue;
    }

    public function deleteWithITunesArtistId(string $iTunesArtistIdValue)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
    }

    public function replaceITunesArtistId(string $oldITunesArtistIdValue, string $iTunesArtistIdValue)
    {
        $this->oldITunesArtistIdValue = $oldITunesArtistIdValue;
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
    }

    public function provisionedPaginator(string $iTunesArtistIdValue = null, string $musicTitleValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->musicTitleValue = $musicTitleValue;
    }

    public function releasedPaginator(string $iTunesArtistIdValue = null, string $musicTitleValue = null)
    {
        $this->iTunesArtistIdValue = $iTunesArtistIdValue;
        $this->musicTitleValue = $musicTitleValue;
    }

    public function getMusicTitle()
    {
        $musicTitleValue = trim($this->musicTitleValue);
        if (empty($musicTitleValue)) {
            return null;
        }
        return new MusicTitle($musicTitleValue);
    }

    public function getBusinessId()
    {
        $iTunesArtistId = $this->getITunesArtistId();
        $musicTitle = $this->getMusicTitle();
        if (empty($iTunesArtistId) || empty($musicTitle)) {
            return null;
        }
        return new MusicBusinessId($iTunesArtistId, $musicTitle);
    }

    public function getITunesBaseUrl()
    {
        $iTunesBaseUrlValue = trim($this->iTunesBaseUrlValue);
        if (empty($iTunesBaseUrlValue)) {
            return null;
        }
        return new ITunesBaseUrl($iTunesBaseUrlValue);
    }

    public function getPromotionVideoUrl()
    {
        $promotionVideoUrlValue = trim($this->promotionVideoUrlValue);
        if (empty($promotionVideoUrlValue)) {
            return null;
        }
        return new PromotionVideoUrl($promotionVideoUrlValue);
    }

    public function getThumbnailUrl()
    {
        $thumbnailUrlValue = trim($this->thumbnailUrlValue);
        if (empty($thumbnailUrlValue)) {
            return null;
        }
        return new ThumbnailUrl($thumbnailUrlValue);
    }

    public function getArtistName()
    {
        $artistNameValue = trim($this->artistNameValue);
        if (empty($artistNameValue)) {
            return null;
        }
        return new ArtistName($artistNameValue);
    }

    public function getCheckPromotionVideoConditions()
    {
        return $this->checkPromotionVideoConditions;
    }

    private function createCheckPromotionVideoConditions()
    {
        if (empty($this->checkPromotionVideoConditions)) {
            $this->checkPromotionVideoConditions = new CheckPromotionVideoConditions();
        }
    }

    public function getItunesArtistIds()
    {
        return $this->iTunesArtistIds;
    }

    public function getOldITunesArtistId()
    {
        $oldITunesArtistIdValue = trim($this->oldITunesArtistIdValue);
        if (empty($oldITunesArtistIdValue)) {
            return null;
        }
        return new ITunesArtistId($oldITunesArtistIdValue);
    }

}
