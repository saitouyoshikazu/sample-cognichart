<?php

namespace App\Domain\Music;
use App\Domain\DomainRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\MusicTitle;
use App\Domain\ValueObjects\CheckPromotionVideoConditions;
use App\Infrastructure\BuilderContainerInterface;

interface MusicRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface       $redisDAO       RedisDAO.
     * @param   MusicFactoryInterface   $musicFactory   MusicFactory.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        MusicFactoryInterface $musicFactory
    );

    /**
     * Find MusicEntity from provisioned phase.
     * @param   EntityId    $id     The id of MusicEntity.
     * @return  MusicEntity     When provisioned MusicEntity was found.
     *          null            When couldn't find provisioned MusicEntity.
     */
    public function findProvision(EntityId $id);

    /**
     * Find MusicEntity from released phase.
     * @param   EntityId    $id     The id of MusicEntity.
     * @return  MusicEntity     When released MusicEntity was found.
     *          null            When couldn't find released MusicEntity.
     */
    public function findRelease(EntityId $id);

    /**
     * Find MusicEntity from cache and storage.
     * @param   EntityId            $entityId               The id of Music.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  MusicEntity     When released MusicEntity was found.
     *          null            When couldn't find released MusicEntity.
     */
    public function findWithCache(EntityId $entityId, MusicSpecification $musicSpecification);

    /**
     * Get MusicEntity from provisioned phase.
     * @param   MusicBusinessId     $musicBusinessId    The business id of Music.
     * @param   EntityId            $excludeId          The id of MusicEntity you want to exclude.
     * @return  MusicEntity     When provisioned Music was found.
     *          null            When couldn't find provisioned MusicEntity.
     */
    public function getProvision(MusicBusinessId $musicBusinessId, EntityId $excludeId = null);

    /**
     * Get MusicEntity from released phase.
     * @param   MusicBusinessId     $musicBusinessId    The business id of Music.
     * @param   EntityId            $excludeId          The id of MusicEntity you want to exclude.
     * @return  MusicEntity     When released Music was found.
     *          null            When couldn't find released MusicEntity.
     */
    public function getRelease(MusicBusinessId $musicBusinessId, EntityId $excludeId = null);

    /**
     * Refresh cached MusicEntity.
     * @param   EntityId            $entityId               The id of MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     */
    public function refreshCachedEntity(EntityId $entityId, MusicSpecification $musicSpecification);

    /**
     * Register MusicEntity to provision.
     * @param   MusicEntity         $musicEntity            MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly stored.
     *          false   When failed to store MusicEntity.
     * @throws  MusicException
     */
    public function register(MusicEntity $musicEntity, MusicSpecification $musicSpecification);

    /**
     * Modify MusicEntity of provision.
     * @param   MusicEntity         $musicEntity            MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly modified.
     *          false   When failed to modify MusicEntity.
     * @throws  MusicException
     */
    public function modifyProvision(MusicEntity $musicEntity, MusicSpecification $musicSpecification);

    /**
     * Delete MusicEntity from provision.
     * @param   EntityId            $id                     The id of MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly deleted.
     *          false   When failed to delete MusicEntity.
     * @throws  MusicException
     */
    public function delete(EntityId $id, MusicSpecification $musicSpecification);

    /**
     * Release provisioned MusicEntity.
     * @param   EntityId            $id                     The id of MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly  released.
     *          false   When failed to release MusicEntity.
     * @throws  MusicException
     */
    public function release(EntityId $id, MusicSpecification $musicSpecification);

    /**
     * Modify MusicEntity of release.
     * @param   MusicEntity         $musicEntity            MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly modified.
     *          false   When failed to modify MusicEntity.
     * @throws  MusicException
     */
    public function modifyRelease(MusicEntity $musicEntity, MusicSpecification $musicSpecification);

    /**
     * Rollback released MusicEntity.
     * @param   EntityId            $id                     The id of MusicEntity.
     * @param   MusicSpecification  $musicSpecification     MusicSpecification.
     * @return  true    When MusicEntity was correctly  released.
     *          false   When failed to release MusicEntity.
     * @throws  MusicException
     */
    public function rollback(EntityId $id, MusicSpecification $musicSpecification);

    /**
     * Get list of MusicEntity is matched CheckPromotionVideoConditions.
     * @param   CheckPromotionVideoConditions   $checkPromotionVideoConditions  CheckPromotionVideoConditions.
     * @return  MusicEntity[]
     */
    public function checkPromotionVideoList(CheckPromotionVideoConditions $checkPromotionVideoConditions);

    /**
     * Register PromotionVideoBrokenLink of MusicEntity.
     * @param   MusicEntity     $musicEntity    MusicEntity.
     * @return  true    When PromotionVideoBrokenLink was correctly stored.
     *          false   When failed to store PromotionVideoBrokenLink.
     */
    public function registerPromotionVideoBrokenLink(MusicEntity $musicEntity);

    /**
     * Delete PromotionVideoBrokenLink of MusicEntity.
     * @param   EntityId    $id     The id of MusicEntity.
     * @return  true    When PromotionVideoBrokenLink was correctly deleted.
     *          false   When failed to delete PromotionVideoBrokenLink.
     */
    public function deletePromotionVideoBrokenLink(EntityId $id);

    /**
     * Return Phase of MusicEntity.
     * @param   EntityId    $id     The id of MusicEntity.
     * @return  Phase   Phase of MusicEntity.
     *          null    When MusicEntity doesn't exist.
     */
    public function getPhase(EntityId $id);

    /**
     * Get list of PromotionVideoBrokenLinks paginated.
     *  @param  array   $musicIds   List of EntityIds of MusicEntity.
     *  @return DomainPaginatorInterface
     */
    public function promotionVideoBrokenLinks(array $musicIds = null);

    /**
     * Get provisioned MusicEntities with parameters.
     * @param   ITunesArtistId      $iTunesArtistId     ITunesArtistId.
     * @param   MusicTitle          $musicTitle         MusicTitle.
     * @param   MusicSpecification  $musicSpecification MusicSpecification.
     * @return  MusicEntity[]   When MusicEntity found.
     *          []              When MusicEntity didn't find.
     */
    public function provisionedEntities(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification);

    /**
     * Get released MusicEntities with parameters.
     * @param   ITunesArtistId      $iTunesArtistId     ITunesArtistId.
     * @param   MusicTitle          $musicTitle         MusicTitle.
     * @param   MusicSpecification  $musicSpecification MusicSpecification.
     * @return  MusicEntity[]   When MusicEntity found.
     *          []              When MusicEntity didn't find.
     */
    public function releasedEntities(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification);

    /**
     * Get list of provisioned MusicEntities with parameters.
     * @param   ITunesArtistId      $iTunesArtistId     ITunesArtistId.
     * @param   MusicTitle          $musicTitle         MusicTitle.
     * @param   MusicSpecification  $musicSpecification MusicSpecification.
     * @return  DomainPaginatorInterface
     */
    public function provisionedPaginator(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification);

    /**
     * Get list of released MusicEntities with parameters.
     * @param   ITunesArtistId      $iTunesArtistId     ITunesArtistId.
     * @param   MusicTitle          $musicTitle         MusicTitle.
     * @param   MusicSpecification  $musicSpecification MusicSpecification.
     * @return  DomainPaginatorInterface
     */
    public function releasedPaginator(ITunesArtistId $iTunesArtistId = null, MusicTitle $musicTitle = null, MusicSpecification $musicSpecification);

    /**
     * Get BuilderContainer that has been set builder to search ProvisionedMusic or Music by ITunesArtistId.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   ITunesArtistId              $iTunesArtistId     ITunesArtistId.
     * @param   string                      $eloquentName       'ProvisionedMusic' or 'Music'.
     * @return  BuilderContainerInterface
     */
    public function builderWithITunesArtistId(BuilderContainerInterface $builderContainer = null, ITunesArtistId $iTunesArtistId = null, string $eloquentName);

    /**
     * Get BuilderContainer that has been set builder to search ProvisionedMusic or Music by MusicTitle.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   MusicTitle                  $musicTitle         MusicTitle.
     * @param   string                      $eloquentName       'ProvisionedMusic' or 'Music'.
     * @return  BuilderContainer
     */
    public function builderWithMusicTitle(BuilderContainerInterface $builderContainer = null, MusicTitle $musicTitle = null, string $eloquentName);

}
