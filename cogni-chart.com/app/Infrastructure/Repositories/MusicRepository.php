<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\Music\MusicRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\Music\MusicFactoryInterface;
use App\Domain\EntityId;
use App\Domain\DomainPaginator;
use App\Domain\ValueObjects\Phase;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\PromotionVideoUrl;
use App\Domain\ValueObjects\ThumbnailUrl;
use App\Domain\ValueObjects\CheckPromotionVideoConditions;
use App\Domain\Music\MusicBusinessId;
use App\Domain\Music\MusicSpecification;
use App\Domain\Music\MusicEntity;
use App\Infrastructure\BuilderContainerInterface;
use App\Infrastructure\Eloquents\Music;
use App\Infrastructure\Eloquents\ProvisionedMusic;
use App\Infrastructure\Eloquents\PromotionVideo;
use App\Infrastructure\Eloquents\PromotionVideoBrokenLink;

class MusicRepository extends DomainRepository implements MusicRepositoryInterface
{

    private $musicFactory;

    public function __construct(
        RedisDAOInterface $redisDAO,
        MusicFactoryInterface $musicFactory
    ) {
        parent::__construct($redisDAO);
        $this->musicFactory = $musicFactory;
    }

    public function findProvision(EntityId $id)
    {
        $row = ProvisionedMusic::find($id->value());
        if (empty($row)) {
            return null;
        }

        return $this->getEntityFromAttributes(
            $row->id,
            $row->itunes_artist_id,
            $row->music_title,
            $row->itunes_base_url
        );
    }

    public function findRelease(EntityId $id)
    {
        $row = Music::find($id->value());
        if (empty($row)) {
            return null;
        }
        return $this->getEntityFromAttributes(
            $row->id,
            $row->itunes_artist_id,
            $row->music_title,
            $row->itunes_base_url
        );
    }

    public function findWithCache(EntityId $entityId, MusicSpecification $musicSpecification)
    {
        return $musicSpecification->findWithCache($entityId, $this);
    }

    public function getProvision(MusicBusinessId $musicBusinessId, EntityId $excludeId = null)
    {
        $provisionedMusic = ProvisionedMusic::businessId(
            $musicBusinessId->iTunesArtistId()->value(),
            $musicBusinessId->musicTitle()->value()
        );
        if (!empty($excludeId)) {
            $provisionedMusic = $provisionedMusic->excludeId($excludeId->value());
        }
        $row = $provisionedMusic->first();
        if (empty($row)) {
            return null;
        }
        return $this->getEntityFromAttributes(
            $row->id,
            $row->itunes_artist_id,
            $row->music_title,
            $row->itunes_base_url
        );
    }

    public function getRelease(MusicBusinessId $musicBusinessId, EntityId $excludeId = null)
    {
        $music = Music::businessId(
            $musicBusinessId->iTunesArtistId()->value(),
            $musicBusinessId->musicTitle()->value()
        );
        if (!empty($excludeId)) {
            $music = $music->excludeId($excludeId->value());
        }
        $row = $music->first();
        if (empty($row)) {
            return null;
        }
        return $this->getEntityFromAttributes(
            $row->id,
            $row->itunes_artist_id,
            $row->music_title,
            $row->itunes_base_url
        );
    }

    public function refreshCachedEntity(EntityId $entityId, MusicSpecification $musicSpecification)
    {
        $musicSpecification->refreshCachedEntity($entityId, $this);
    }

    public function register(MusicEntity $musicEntity, MusicSpecification $musicSpecification)
    {
        $musicSpecification->register($musicEntity, $this);
        $iTunesBaseUrlValue = '';
        if (!empty($musicEntity->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $musicEntity->iTunesBaseUrl()->value();
        }
        $parameters = [
            'id'                =>  $musicEntity->id()->value(),
            'itunes_artist_id'  =>  $musicEntity->iTunesArtistId()->value(),
            'music_title'       =>  $musicEntity->musicTitle()->value(),
            'itunes_base_url'   =>  $iTunesBaseUrlValue
        ];
        $provisionedMusic = new ProvisionedMusic();
        if (!$provisionedMusic->fill($parameters)->save()) {
            return false;
        }
        return $this->registerPromotionVideo($musicEntity->id(), $musicEntity->promotionVideoUrl(), $musicEntity->thumbnailUrl());
    }

    public function modifyProvision(MusicEntity $musicEntity, MusicSpecification $musicSpecification)
    {
        $musicSpecification->modifyProvision($musicEntity, $this);
        $iTunesBaseUrlValue = '';
        if (!empty($musicEntity->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $musicEntity->iTunesBaseUrl()->value();
        }
        $parameters = [
            'itunes_artist_id'  =>  $musicEntity->iTunesArtistId()->value(),
            'music_title'       =>  $musicEntity->musicTitle()->value(),
            'itunes_base_url'   =>  $iTunesBaseUrlValue
        ];
        $result = ProvisionedMusic::find($musicEntity->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        if (!$this->deletePromotionVideo($musicEntity->id())) {
            return false;
        }
        return $this->registerPromotionVideo($musicEntity->id(), $musicEntity->promotionVideoUrl(), $musicEntity->thumbnailUrl());
    }

    public function delete(EntityId $id, MusicSpecification $musicSpecification)
    {
        $musicSpecification->delete($id, $this);
        if (!$this->deletePromotionVideo($id)) {
            return false;
        }
        $result = ProvisionedMusic::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return true;
    }

    public function release(EntityId $id, MusicSpecification $musicSpecification)
    {
        $releaseTarget = $musicSpecification->release($id, $this);
        $result = ProvisionedMusic::destroy($releaseTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $iTunesBaseUrlValue = '';
        if (!empty($releaseTarget->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $releaseTarget->iTunesBaseUrl()->value();
        }
        $parameters = [
            'id'                =>  $releaseTarget->id()->value(),
            'itunes_artist_id'  =>  $releaseTarget->iTunesArtistId()->value(),
            'music_title'       =>  $releaseTarget->musicTitle()->value(),
            'itunes_base_url'   =>  $iTunesBaseUrlValue
        ];
        $music = new Music();
        if (!$music->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    public function modifyRelease(MusicEntity $musicEntity, MusicSpecification $musicSpecification)
    {
        $musicSpecification->modifyRelease($musicEntity, $this);
        $iTunesBaseUrlValue = '';
        if (!empty($musicEntity->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $musicEntity->iTunesBaseUrl()->value();
        }
        $parameters = [
            'itunes_artist_id'  =>  $musicEntity->iTunesArtistId()->value(),
            'music_title'       =>  $musicEntity->musicTitle()->value(),
            'itunes_base_url'   =>  $iTunesBaseUrlValue
        ];
        $result = Music::find($musicEntity->id()->value())->fill($parameters)->save();
        if ($result !== true) {
            return false;
        }
        if (!$this->deletePromotionVideo($musicEntity->id())) {
            return false;
        }
        return $this->registerPromotionVideo($musicEntity->id(), $musicEntity->promotionVideoUrl(), $musicEntity->thumbnailUrl());
    }

    public function rollback(EntityId $id, MusicSpecification $musicSpecification)
    {
        $rollbackTarget = $musicSpecification->rollback($id, $this);
        $result = Music::destroy($rollbackTarget->id()->value());
        if ($result !== 1) {
            return false;
        }
        $iTunesBaseUrlValue = '';
        if (!empty($rollbackTarget->iTunesBaseUrl())) {
            $iTunesBaseUrlValue = $rollbackTarget->iTunesBaseUrl()->value();
        }
        $parameters = [
            'id'                =>  $rollbackTarget->id()->value(),
            'itunes_artist_id'  =>  $rollbackTarget->iTunesArtistId()->value(),
            'music_title'       =>  $rollbackTarget->musicTitle()->value(),
            'itunes_base_url'   =>  $iTunesBaseUrlValue
        ];
        $provisionedMusic = new ProvisionedMusic();
        if (!$provisionedMusic->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    protected function idExisting(EntityId $id)
    {
        $row = Music::find($id->value());
        if (!empty($row)) {
            return true;
        }
        $row = ProvisionedMusic::find($id->value());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

    private function promotionVideoRow(string $musicIdValue)
    {
        $musicIdValue = trim($musicIdValue);
        if (empty($musicIdValue)) {
            return null;
        }
        $promotionVideo = PromotionVideo::musicId($musicIdValue)->first();
        if (empty($promotionVideo)) {
            return null;
        }
        return $promotionVideo;
    }

    private function registerPromotionVideo(EntityId $id, PromotionVideoUrl $promotionVideoUrl = null, ThumbnailUrl $thumbnailUrl = null)
    {
        if (empty($promotionVideoUrl)) {
            return true;
        }
        $parameters = [
            'music_id'      =>  $id->value(),
            'url'           =>  $promotionVideoUrl->value(),
            'thumbnail_url' =>  empty($thumbnailUrl) ? null : $thumbnailUrl->value()
        ];
        $promotionVideo = new PromotionVideo();
        return $promotionVideo->fill($parameters)->save();
    }

    public function deletePromotionVideo(EntityId $id)
    {
        $rows = PromotionVideo::musicId($id->value())->get();
        if (empty($rows)) {
            return true;
        }
        if ($rows->count() === 0) {
            return true;
        }
        $result = PromotionVideo::musicId($id->value())->delete();
        if ($result === 0) {
            return false;
        }
        return true;
    }

    public function checkPromotionVideoList(CheckPromotionVideoConditions $checkPromotionVideoConditions)
    {
        $conditions = $checkPromotionVideoConditions->getConditions();

        $music = null;
        $provisionedMusic = null;
        foreach ($conditions AS $condition) {
            $scope = $condition["scope"];
            $param = $condition["param"];
            if (empty($music)) {
                $music = Music::$scope($param);
            } else {
                $music->$scope($param);
            }
            if (empty($provisionedMusic)) {
                $provisionedMusic = ProvisionedMusic::$scope($param);
            } else {
                $provisionedMusic->$scope($param);
            }
        }

        $musicEntities = [];
        if (!empty($music)) {
            $rows = $music->get();
            if (!empty($rows)) {
                foreach ($rows AS $row) {
                    $musicEntity = $this->getEntityFromAttributes(
                        $row->id,
                        $row->itunes_artist_id,
                        $row->music_title,
                        $row->itunes_base_url
                    );
                    if (!empty($musicEntity)) {
                        $musicEntities[] = $musicEntity;
                    }
                }
            }
        }
        if (!empty($provisionedMusic)) {
            $rows = $provisionedMusic->get();
            if (!empty($rows)) {
                foreach ($rows AS $row) {
                    $musicEntity = $this->getEntityFromAttributes(
                        $row->id,
                        $row->itunes_artist_id,
                        $row->music_title,
                        $row->itunes_base_url
                    );
                    if (!empty($musicEntity)) {
                        $musicEntities[] = $musicEntity;
                    }
                }
            }
        }
        return $musicEntities;
    }

    public function registerPromotionVideoBrokenLink(MusicEntity $musicEntity)
    {
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicId($musicEntity->id()->value())->first();
        if (!empty($promotionVideoBrokenLink)) {
            return true;
        }
        $parameters = [
            "music_id"  =>  $musicEntity->id()->value()
        ];
        $promotionVideoBrokenLink = new PromotionVideoBrokenLink();
        if (!$promotionVideoBrokenLink->fill($parameters)->save()) {
            return false;
        }
        return true;
    }

    public function deletePromotionVideoBrokenLink(EntityId $id)
    {
        $rows = PromotionVideoBrokenLink::musicId($id->value())->get();
        if (empty($rows)) {
            return true;
        }
        if ($rows->count() === 0) {
            return true;
        }
        $result = PromotionVideoBrokenLink::musicId($id->value())->delete();
        if ($result === 0) {
            return false;
        }
        return true;
    }

    public function getPhase(EntityId $id)
    {
        if (!empty($this->findProvision($id))) {
            return new Phase(Phase::provisioned);
        }
        if (!empty($this->findRelease($id))) {
            return new Phase(Phase::released);
        }
        return null;
    }

    public function promotionVideoBrokenLinks(array $musicIds = null)
    {
        $musicEntities = [];
        $promotionVideoBrokenLink = PromotionVideoBrokenLink::query();
        if (!empty($musicIds)) {
            $idValues = [];
            foreach ($musicIds AS $entityId) {
                $idValues[] = $entityId->value();
            }
            $promotionVideoBrokenLink = PromotionVideoBrokenLink::musicIds($idValues);
        }
        $rows = $promotionVideoBrokenLink
            ->searchOrder('created_at', 'desc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $musicEntity = $this->findRelease(new EntityId($row->music_id));
            if (empty($musicEntity)) {
                $musicEntity = $this->findProvision(new EntityId($row->music_id));
            }
            if (!empty($musicEntity)) {
                $musicEntities[] = $musicEntity;
            }
        }
        return new DomainPaginator($musicEntities, $rows);
    }

    public function provisionedEntities(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification)
    {
        $musicEntities = [];
        $provisionedMusics = $musicSpecification
            ->buildQuery($iTunesArtistId, $musicTitle, 'ProvisionedMusic', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('music_title', 'asc')
            ->get();
        if (empty($provisionedMusics)) {
            return $musicEntities;
        }
        foreach ($provisionedMusics AS $provisionedMusic) {
            $musicEntities[] = $this->getEntityFromAttributes(
                $provisionedMusic->id,
                $provisionedMusic->itunes_artist_id,
                $provisionedMusic->music_title,
                $provisionedMusic->itunes_base_url
            );
        }
        return $musicEntities;
    }

    public function releasedEntities(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification)
    {
        $musicEntities = [];
        $musics = $musicSpecification
            ->buildQuery($iTunesArtistId, $musicTitle, 'Music', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('music_title', 'asc')
            ->get();
        if (empty($musics)) {
            return $musicEntities;
        }
        foreach ($musics AS $music) {
            $musicEntities[] = $this->getEntityFromAttributes(
                $music->id,
                $music->itunes_artist_id,
                $music->music_title,
                $music->itunes_base_url
            );
        }
        return $musicEntities;
    }

    public function provisionedPaginator(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification)
    {
        $musicEntities = [];
        $rows = $musicSpecification
            ->buildQuery($iTunesArtistId, $musicTitle, 'ProvisionedMusic', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('music_title', 'asc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $musicEntities[] = $this->getEntityFromAttributes(
                $row->id,
                $row->itunes_artist_id,
                $row->music_title,
                $row->itunes_base_url
            );
        }
        return new DomainPaginator($musicEntities, $rows);
    }

    public function releasedPaginator(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification)
    {
        $musicEntities = [];
        $rows = $musicSpecification
            ->buildQuery($iTunesArtistId, $musicTitle, 'Music', $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('music_title', 'asc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $musicEntities[] = $this->getEntityFromAttributes(
                $row->id,
                $row->itunes_artist_id,
                $row->music_title,
                $row->itunes_base_url
            );
        }
        return new DomainPaginator($musicEntities, $rows);
    }

    public function builderWithITunesArtistId(BuilderContainerInterface $builderContainer = null, ITunesArtistId $iTunesArtistId = null, string $eloquentName)
    {
        $builderContainer = $this->initBuilder($builderContainer, $eloquentName);
        $builder = $builderContainer->get($eloquentName);
        if (!empty($iTunesArtistId)) {
            $builder = $builder->iTunesArtistId($iTunesArtistId->value());
        }
        $builderContainer->set($eloquentName, $builder);
        return $builderContainer;
    }

    public function builderWithMusicTitle(BuilderContainerInterface $builderContainer = null, MusicTitle $musicTitle = null, string $eloquentName)
    {
        $builderContainer = $this->initBuilder($builderContainer, $eloquentName);
        $builder = $builderContainer->get($eloquentName);
        if (!empty($musicTitle)) {
            $builder = $builder->musicTitleLike($musicTitle->value());
        }
        $builderContainer->set($eloquentName, $builder);
        return $builderContainer;
    }

    private function getEntityFromAttributes(
        string $idValue,
        string $itunesArtistIdValue,
        string $musicTitleValue,
        string $iTunesBaseUrlValue = null
    ) {
        $promotionVideoUrlValue = null;
        $thumbnailUrlValue = null;
        $promotionVideoRow = $this->promotionVideoRow($idValue);
        if (!empty($promotionVideoRow)) {
            $promotionVideoUrlValue = $promotionVideoRow->url;
            $thumbnailUrlValue = $promotionVideoRow->thumbnail_url;
        }
        return $this->musicFactory->create(
            $idValue,
            $itunesArtistIdValue,
            $musicTitleValue,
            $iTunesBaseUrlValue,
            $promotionVideoUrlValue,
            $thumbnailUrlValue
        );
    }

}
