<?php

namespace App\Domain\Artist;
use App\Domain\DomainRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\EntityId;
use App\Domain\ValueObjects\ITunesArtistId;
use App\Domain\ValueObjects\ArtistName;
use App\Infrastructure\BuilderContainerInterface;

interface ArtistRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface       $redisDAO       RedisDAO.
     * @param   ArtistFactoryInterface  $artistFactory  ArtistFactory.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        ArtistFactoryInterface $artistFactory
    );

    /**
     * Find ArtistEntity from provisioned phase.
     * @param   EntityId        $id     The id of ArtistEntity.
     * @return  ArtistEntity    When provisioned ArtistEntity was found.
     *          null            When couldn't find provisioned ArtistEntity.
     */
    public function findProvision(EntityId $id);

    /**
     * Find ArtistEntity from released phase.
     * @param   EntityId        $id     The id of ArtistEntity.
     * @return  ArtistEntity    When released ArtistEntity was found.
     *          null            When couldn't find released ArtistEntity.
     */
    public function findRelease(EntityId $id);

    /**
     * Find ArtistEntity from cache and storage.
     * @param   EntityId                $entityId               The id of Artist.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  ArtistEntity    When released ArtistEntity was found.
     *          null            When couldn't find released ArtistEntity.
     */
    public function findWithCache(EntityId $entityId, ArtistSpecification $artistSpecification);

    /**
     * Get ArtistEntity from provisioned phase.
     * @param   ArtistBusinessId    $artistBusinessId   The business id of Artist.
     * @param   EntityId            $excludeId          The id of ArtistEntity you want to exclude.
     * @return  ArtistEntity    When provisioned Artist was found.
     *          null            When couldn't find provisioned ArtistEntity.
     */
    public function getProvision(ArtistBusinessId $artistBusinessId, EntityId $excludeId = null);

    /**
     * Get ArtistEntity from released phase.
     * @param   ArtistBusinessId    $artistBusinessId   The business id of Artist.
     * @param   EntityId            $excludeId          The id of ArtistEntity you want to exclude.
     * @return  ArtistEntity    When released Artist was found.
     *          null            When couldn't find released ArtistEntity.
     */
    public function getRelease(ArtistBusinessId $artistBusinessId, EntityId $excludeId = null);

    /**
     * Refresh cached ArtistEntity.
     * @param   EntityId                $entityId               The id of ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     */
    public function refreshCachedEntity(EntityId $entityId, ArtistSpecification $artistSpecification);

    /**
     * Register ArtistEntity to provision.
     * @param   ArtistEntity            $artistEntity           ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly stored.
     *          false   When failed to store ArtistEntity.
     * @throws  ArtistException
     */
    public function register(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification);

    /**
     * Modify ArtistEntity of provision.
     * @param   ArtistEntity            $artistEntity           ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly modified.
     *          false   When failed to modify ArtistEntity.
     * @throws  ArtistException
     */
    public function modifyProvision(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification);

    /**
     * Delete ArtistEntity from provision.
     * @param   EntityId                $id                     The id of ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly deleted.
     *          false   When failed to delete ArtistEntity.
     * @throws  ArtistException
     */
    public function delete(EntityId $id, ArtistSpecification $artistSpecification);

    /**
     * Release provisioned ArtistEntity.
     * @param   EntityId                $id                     The id of ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly  released.
     *          false   When failed to release ArtistEntity.
     * @throws  ArtistException
     */
    public function release(EntityId $id, ArtistSpecification $artistSpecification);

    /**
     * Modify ArtistEntity of release.
     * @param   ArtistEntity            $artistEntity           ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly modified.
     *          false   When failed to modify ArtistEntity.
     * @throws  ArtistException
     */
    public function modifyRelease(ArtistEntity $artistEntity, ArtistSpecification $artistSpecification);

    /**
     * Rollback released ArtistEntity.
     * @param   EntityId                $id                     The id of ArtistEntity.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  true    When ArtistEntity was correctly  released.
     *          false   When failed to release ArtistEntity.
     * @throws  ArtistException
     */
    public function rollback(EntityId $id, ArtistSpecification $artistSpecification);

    /**
     * Get provisioned ArtistEntities with parameters.
     * @param   ITunesArtistId          $iTunesArtistId         ITunesArtistId.
     * @param   ArtistName              $artistName             ArtistName.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  ArtistEntity[]  When ArtistEntity found.
     *          []              When ArtistEntity didn't find.
     */
    public function provisionedEntities(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification);

    /**
     * Get released ArtistEntities with parameters.
     * @param   ITunesArtistId          $iTunesArtistId         ITunesArtistId.
     * @param   ArtistName              $artistName             ArtistName.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  ArtistEntity[]  When ArtistEntity found.
     *          []              When ArtistEntity didn't find.
     */
    public function releasedEntities(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification);

    /**
     * Get list of provisioned ArtistEntities with parameters.
     * @param   ITunesArtistId          $iTunesArtistId         ITunesArtistId.
     * @param   ArtistName              $artistName             ArtistName.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  DomainPaginatorInterface
     */
    public function provisionedPaginator(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification);

    /**
     * Get list of released ArtistEntities with parameters.
     * @param   ITunesArtistId          $iTunesArtistId         ITunesArtistId.
     * @param   ArtistName              $artistName             ArtistName.
     * @param   ArtistSpecification     $artistSpecification    ArtistSpecification.
     * @return  DomainPaginatorInterface
     */
    public function releasedPaginator(ITunesArtistId $iTunesArtistId = null, ArtistName $artistName = null, ArtistSpecification $artistSpecification);

    /**
     * Get BuilderContainer that has been set builder to search ProvisionedArtist or Artist by ITunesArtistId.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   ITunesArtistId              $iTunesArtistId     ITunesArtistId.
     * @param   string                      $eloquentName       'ProvisionedArtist' or 'Artist'.
     * @return  BuilderContainerInterface
     */
    public function builderWithITunesArtistId(BuilderContainerInterface $builderContainer = null, ITunesArtistId $iTunesArtistId = null, string $eloquentName);

    /**
     * Get BuilderContainer that has been set builder to search ProvisionedArtist or Artist by ArtistName.
     * @param   BuilderContainerInterface   $builderContainer   BuilderContainer.
     * @param   ArtistName                  $artistName         ArtistName.
     * @param   string                      $eloquentName       'ProvisionedArtist' or 'Artist'.
     * @return  BuilderContainerInterface
     */
    public function builderWithArtistName(BuilderContainerInterface $builderContainer = null, ArtistName $artistName = null, string $eloquentName);

}
