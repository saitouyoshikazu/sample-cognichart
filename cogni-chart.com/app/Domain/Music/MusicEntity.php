<?php

namespace App\Domain\Music;
use App\Domain\Entity;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\ITunesBaseUrl;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;

class MusicEntity extends Entity
{

    private $iTunesArtistId;
    private $musicTitle;
    private $iTunesBaseUrl;
    private $promotionVideoUrl;
    private $thumbnailUrl;

    public function __construct(EntityId $id, ITunesArtistId $iTunesArtistId, MusicTitle $musicTitle)
    {
        parent::__construct($id);
        $this
            ->setITunesArtistId($iTunesArtistId)
            ->setMusicTitle($musicTitle);
    }

    public function setITunesArtistId(ITunesArtistId $iTunesArtistId)
    {
        $this->iTunesArtistId = $iTunesArtistId;
        $this->setBusinessId();
        return $this;
    }

    public function iTunesArtistId()
    {
        return $this->iTunesArtistId;
    }

    public function setMusicTitle(MusicTitle $musicTitle)
    {
        $this->musicTitle = $musicTitle;
        $this->setBusinessId();
        return $this;
    }

    public function musicTitle()
    {
        return $this->musicTitle;
    }

    protected function setBusinessId()
    {
        if (empty($this->iTunesArtistId) || empty($this->musicTitle)) {
            $this->businessId = null;
            return;
        }
        if (empty($this->businessId)) {
            $this->businessId = new MusicBusinessId($this->iTunesArtistId, $this->musicTitle);
            return;
        }
        $this->businessId
            ->setITunesArtistId($this->iTunesArtistId)
            ->setMusicTitle($this->musicTitle);
    }

    public function setITunesBaseUrl(ITunesBaseUrl $iTunesBaseUrl = null)
    {
        $this->iTunesBaseUrl = $iTunesBaseUrl;
        return $this;
    }

    public function iTunesBaseUrl()
    {
        return $this->iTunesBaseUrl;
    }

    public function setPromotionVideoUrl(PromotionVideoUrl $promotionVideoUrl = null)
    {
        $this->promotionVideoUrl = $promotionVideoUrl;
        return $this;
    }

    public function promotionVideoUrl()
    {
        return $this->promotionVideoUrl;
    }

    public function setThumbnailUrl(ThumbnailUrl $thumbnailUrl = null)
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    public function thumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

}
