<?php

namespace App\Infrastructure\Repositories;
use App\Domain\DomainRepository;
use App\Domain\ChartRankingItem\ChartRankingItemRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface;
use App\Domain\EntityId;
use App\Domain\DomainPaginator;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ChartRankingItem\ChartRankingItemBusinessId;
use App\Domain\ChartRankingItem\ChartRankingItemEntity;
use App\Domain\ChartRankingItem\ChartRankingItemSpecification;
use App\Infrastructure\BuilderContainerInterface;
use App\Infrastructure\Eloquents\ChartRankingItem;

class ChartRankingItemRepository extends DomainRepository implements ChartRankingItemRepositoryInterface
{

    private $eloquentName = 'ChartRankingItem';
    private $chartRankingItemFactory;

    public function __construct(
        RedisDAOInterface $redisDAO,
        ChartRankingItemFactoryInterface $chartRankingItemFactory
    ) {
        parent::__construct($redisDAO);
        $this->chartRankingItemFactory = $chartRankingItemFactory;
    }

    public function find(EntityId $id)
    {
        $row = ChartRankingItem::find($id->value());
        if (empty($row)) {
            return null;
        }
        $chartRankingItemEntity = $this->chartRankingItemFactory->create(
            $row->id,
            $row->chart_artist,
            $row->chart_music,
            $row->artist_id,
            $row->music_id
        );
        return $chartRankingItemEntity;
    }

    public function findWithCache(EntityId $entityId, ChartRankingItemSpecification $chartRankingItemSpecification)
    {
        return $chartRankingItemSpecification->findWithCache($entityId, $this);
    }

    public function get(ChartRankingItemBusinessId $chartRankingItemBusinessId, EntityId $excludeId = null)
    {
        $chartRankingItem = ChartRankingItem::businessId($chartRankingItemBusinessId->chartArtist()->value(), $chartRankingItemBusinessId->chartMusic()->value());
        if (!empty($excludeId)) {
            $chartRankingItem->excludeId($excludeId->value());
        }
        $row = $chartRankingItem->first();
        if (empty($row)) {
            return null;
        }
        $chartRankingItemEntity = $this->chartRankingItemFactory->create(
            $row->id,
            $row->chart_artist,
            $row->chart_music,
            $row->artist_id,
            $row->music_id
        );
        return $chartRankingItemEntity;
    }

    public function refreshCachedEntity(EntityId $entityId, ChartRankingItemSpecification $chartRankingItemSpecification)
    {
        $chartRankingItemSpecification->refreshCachedEntity($entityId, $this);
    }

    public function register(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemSpecification $chartRankingItemSpecification)
    {
        $chartRankingItemSpecification->register($chartRankingItemEntity, $this);
        $artistIdValue = '';
        if (!empty($chartRankingItemEntity->artistId())) {
            $artistIdValue = $chartRankingItemEntity->artistId()->value();
        }
        $musicIdValue = '';
        if (!empty($chartRankingItemEntity->musicId())) {
            $musicIdValue = $chartRankingItemEntity->musicId()->value();
        }
        $parameters = [
            'id'            =>  $chartRankingItemEntity->id()->value(),
            'chart_artist'  =>  $chartRankingItemEntity->chartArtist()->value(),
            'chart_music'   =>  $chartRankingItemEntity->chartMusic()->value(),
            'artist_id'     =>  $artistIdValue,
            'music_id'      =>  $musicIdValue
        ];
        $chartRankingItem = new ChartRankingItem();
        return $chartRankingItem->fill($parameters)->save();
    }

    public function modify(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemSpecification $chartRankingItemSpecification)
    {
        $chartRankingItemSpecification->modify($chartRankingItemEntity, $this);
        $artistIdValue = '';
        if (!empty($chartRankingItemEntity->artistId())) {
            $artistIdValue = $chartRankingItemEntity->artistId()->value();
        }
        $musicIdValue = '';
        if (!empty($chartRankingItemEntity->musicId())) {
            $musicIdValue = $chartRankingItemEntity->musicId()->value();
        }
        $parameters = [
            'id'            =>  $chartRankingItemEntity->id()->value(),
            'chart_artist'  =>  $chartRankingItemEntity->chartArtist()->value(),
            'chart_music'   =>  $chartRankingItemEntity->chartMusic()->value(),
            'artist_id'     =>  $artistIdValue,
            'music_id'      =>  $musicIdValue
        ];
        return ChartRankingItem::find($chartRankingItemEntity->id()->value())->fill($parameters)->save();
    }

    public function delete(EntityId $id, ChartRankingItemSpecification $chartRankingItemSpecification)
    {
        $chartRankingItemSpecification->delete($id, $this);
        $result = ChartRankingItem::destroy($id->value());
        if ($result !== 1) {
            return false;
        }
        return true;
    }

    public function entities(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        EntityId $artistId = null,
        EntityId $musicId = null,
        ChartRankingItemSpecification $chartRankingItemSpecification
    ) {
        $chartRankingItemEntities = [];
        $rows = $chartRankingItemSpecification
            ->buildQuery($chartArtist, $chartMusic, $artistId, $musicId, $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('chart_artist', 'asc')
            ->searchOrder('chart_music', 'asc')
            ->get();
        if (empty($rows)) {
            return $chartRankingItemEntities;
        }
        foreach ($rows AS $row) {
            $chartRankingItemEntity = $this->chartRankingItemFactory->create(
                $row->id,
                $row->chart_artist,
                $row->chart_music,
                $row->artist_id,
                $row->music_id
            );
            if (!empty($chartRankingItemEntity)) {
                $chartRankingItemEntities[] = $chartRankingItemEntity;
            }
        }
        return $chartRankingItemEntities;
    }

    public function notAttachedPaginator(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        ChartRankingItemSpecification $chartRankingItemSpecification
    ) {
        $chartRankingItemEntities = [];
        $rows = $chartRankingItemSpecification
            ->notAttachedQuery($chartArtist, $chartMusic, $this)
            ->searchOrder('created_at', 'desc')
            ->searchOrder('chart_artist', 'asc')
            ->searchOrder('chart_music', 'asc')
            ->executePaginate();
        foreach ($rows AS $row) {
            $chartRankingItemEntity = $this->chartRankingItemFactory->create(
                $row->id,
                $row->chart_artist,
                $row->chart_music,
                $row->artist_id,
                $row->music_id
            );
            if (!empty($chartRankingItemEntity)) {
                $chartRankingItemEntities[] = $chartRankingItemEntity;
            }
        }
        return new DomainPaginator($chartRankingItemEntities, $rows);
    }

    public function builderWithChartArtist(BuilderContainerInterface $builderContainer = null, ChartArtist $chartArtist = null)
    {
        $builderContainer = $this->initBuilder($builderContainer, $this->eloquentName);
        $builder = $builderContainer->get($this->eloquentName);
        if (!empty($chartArtist)) {
            $builder = $builder->chartArtistLike($chartArtist->value());
        }
        $builderContainer->set($this->eloquentName, $builder);
        return $builderContainer;
    }

    public function builderWithChartMusic(BuilderContainerInterface $builderContainer = null, ChartMusic $chartMusic = null)
    {
        $builderContainer = $this->initBuilder($builderContainer, $this->eloquentName);
        $builder = $builderContainer->get($this->eloquentName);
        if (!empty($chartMusic)) {
            $builder = $builder->chartMusicLike($chartMusic->value());
        }
        $builderContainer->set($this->eloquentName, $builder);
        return $builderContainer;
    }

    public function builderWithArtistId(BuilderContainerInterface $builderContainer = null, EntityId $artistId = null)
    {
        $builderContainer = $this->initBuilder($builderContainer, $this->eloquentName);
        $builder = $builderContainer->get($this->eloquentName);
        if (!empty($artistId)) {
            $builder = $builder->artistId($artistId->value());
        }
        $builderContainer->set($this->eloquentName, $builder);
        return $builderContainer;
    }

    public function builderWithMusicId(BuilderContainerInterface $builderContainer = null, EntityId $musicId = null)
    {
        $builderContainer = $this->initBuilder($builderContainer, $this->eloquentName);
        $builder = $builderContainer->get($this->eloquentName);
        if (!empty($musicId)) {
            $builder = $builder->musicId($musicId->value());
        }
        $builderContainer->set($this->eloquentName, $builder);
        return $builderContainer;
    }

    public function builderNotAttached(BuilderContainerInterface $builderContainer = null)
    {
        $builderContainer = $this->initBuilder($builderContainer, $this->eloquentName);
        $builder = $builderContainer->get($this->eloquentName);
        $builder = $builder->notAttached();
        $builderContainer->set($this->eloquentName, $builder);
        return $builderContainer;
    }

    protected function idExisting(EntityId $id)
    {
        $row = ChartRankingItem::find($id->value());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

}
