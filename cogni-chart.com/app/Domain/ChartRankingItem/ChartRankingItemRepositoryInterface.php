<?php

namespace App\Domain\ChartRankingItem;
use App\Domain\DomainRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\ChartRankingItem\ChartRankingItemFactoryInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ChartArtist;
use App\Domain\ValueObjects\ChartMusic;
use App\Domain\ChartRankingItem\ChartRankingItemSpecification;
use App\Infrastructure\BuilderContainerInterface;

interface ChartRankingItemRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface                   $redisDAO                   RedisDAO.
     * @param   ChartRankingItemFactoryInterface    $chartRankingItemFactory    ChartRankingItemFactory.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        ChartRankingItemFactoryInterface $chartRankingItemFactory
    );

    /**
     * Find ChartRankingItemEntity.
     * @param   EntityId    $id     The id of ChartRankingItemEntity.
     * @return  ChartRankingItemEntity  When ChartRankingItemEntity was found.
     *          null                    When couldn't find ChartRankingItemEntity.
     * @throws  ValueObjectException
     */
    public function find(EntityId $id);

    /**
     * Find ChartRankingItemEntity from cache and storage.
     * @param   EntityId                        $id                             The id of ChartRankingItemEntity.
     * @param   ChartRankingItemSpecification   $chartRankingItemSpecification  ChartRankingItemSpecification.
     * @return  ChartRankingItemEntity  When ChartRankingItemEntity was found.
     *          null                    When couldn't find ChartRankingItemEntity.
     * @throws  ValueObjectException
     */
    public function findWithCache(EntityId $id, ChartRankingItemSpecification $chartRankingItemSpecification);

    /**
     * Get ChartRankingItemEntity.
     * @param   ChartRankingItemBusinessId  $chartRankingItemBusinessId     The business id of ChartRankingItem.
     * @param   EntityId                    $excludeId                      The id of ChartRankingItemEntity you want to exclude.
     * @return  ChartRankingItemEntity  When ChartRankingItemEntity was found.
     *          null                    When couldn't find ChartRankingItemEntity.
     * @throws  ValueObjectException
     */
    public function get(ChartRankingItemBusinessId $chartRankingItemBusinessId, EntityId $excludeId = null);

    /**
     * Refresh cached ChartRankingItemEntity.
     * @param   EntityId                        $entityId                       The id of ChartRankingItem that will be stored to cache.
     * @param   ChartRankingItemSpecification   $chartRankingItemSpecification  ChartRankingItemSpecification.
     */
    public function refreshCachedEntity(EntityId $entityId, ChartRankingItemSpecification $chartRankingItemSpecification);

    /**
     * Register ChartRankingItemEntity to storage.
     * @param   ChartRankingItemEntity          $chartRankingItemEntity         ChartRankingItemEntity.
     * @param   ChartRankingItemSpecification   $chartRankingItemSpecification  ChartRankingItemSpecification.
     * @return  true    When ChartRankingItemEntity is correctly registered.
     *          false   When failed to register ChartRankingItemEntity.
     * @throws  ChartRankingItemException
     */
    public function register(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemSpecification $chartRankingItemSpecification);

    /**
     * Modify ChartRankingItemEntity of storage.
     * @param   ChartRankingItemEntity          $chartRankingItemEntity         ChartRankingItemEntity.
     * @param   ChartRankingItemSpecification   $chartRankingItemSpecification  ChartRankingItemSpecification.
     * @return  true    When ChartRankingItemEntity is correctly modified.
     *          false   When failed to modify ChartRankingItemEntity.
     * @throws  ChartRankingItemException
     */
    public function modify(ChartRankingItemEntity $chartRankingItemEntity, ChartRankingItemSpecification $chartRankingItemSpecification);

    /**
     * Delete ChartRankingItemEntity from storage.
     * @param   EntityId                        $id                             The id of ChartRankingItem.
     * @param   ChartRankingItemSpecification   $chartRankingItemSpecification  ChartRankingItemSpecification.
     * @return  true    When ChartRankingItemEntity is correctly deleted.
     *          false   When failed to delete ChartRankingItemEntity.
     * @throws  ChartRankingItemException
     */
    public function delete(EntityId $id, ChartRankingItemSpecification $chartRankingItemSpecification);

    /**
     * Get ChartRankingItemEntities with parameters.
     * @param   ChartArtist     $chartArtist    ChartArtist;
     * @param   ChartMusic      $chartMusic     ChartMusic.
     * @param   EntityId        $artistId       The id of Artist.
     * @param   EntityId        $musicId        The id of Music.
     * @return  ChartRankingItemEntity[]    When ChartRankingItemEntity found.
     *          []                          When ChartRankingItemEntity didn't find.
     */
    public function entities(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        EntityId $artistId = null,
        EntityId $musicId = null,
        ChartRankingItemSpecification $chartRankingItemSpecification
    );

    /**
     * Get list of ChartRankingItemEntities that is not attached ArtistId or MusicId with parameters.
     * @param   ChartArtist     $chartArtist    ChartArtist;
     * @param   ChartMusic      $chartMusic     ChartMusic.
     * @return  DomainPaginatorInterface
     */
    public function notAttachedPaginator(
        ChartArtist $chartArtist = null,
        ChartMusic $chartMusic = null,
        ChartRankingItemSpecification $chartRankingItemSpecification
    );

    /**
     * Get BuilderContainer that has been set builder to search ChartRankingItem by ChartArtist.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   ChartArtist                 $chartArtist        ChartArtist.
     * @return  BuilderContainerInterface
     */
    public function builderWithChartArtist(BuilderContainerInterface $builderContainer = null, ChartArtist $chartArtist = null);

    /**
     * Get BuilderContainer that has been set builder to search ChartRankingItem by ChartMusic.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   ChartMusic                  $chartMusic         ChartMusic.
     * @return  BuilderContainerInterface
     */
    public function builderWithChartMusic(BuilderContainerInterface $builderContainer = null, ChartMusic $chartMusic = null);

    /**
     * Get BuilderContainer that has been set builder to search ChartRankingItem by ArtistId.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   EntityId                    $artistId           The id of Artist.
     * @return  BuilderContainerInterface
     */
    public function builderWithArtistId(BuilderContainerInterface $builderContainer = null, EntityId $artistId = null);

    /**
     * Get BuilderContainer that has been set builder to search ChartRankingItem by MusicId.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   EntityId                    $musicId            The id of Music.
     * @return  BuilderContainerInterface
     */
    public function builderWithMusicId(BuilderContainerInterface $builderContainer = null, EntityId $musicId = null);

    /**
     * Get BuilderContainer that has been set builder to search ChartRankingItem by being not attached ArtistId or MusicId.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @return  BuilderContainerInterface
     */
    public function builderNotAttached(BuilderContainerInterface $builderContainer = null);

}
