<?php

namespace App\Domain\ChartTerm;
use App\Domain\DomainRepositoryInterface;
use App\Domain\EntityId;
use App\Infrastructure\RedisDAO\RedisDAOInterface;

interface ChartTermRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface           $redisDAO           RedisDAO.
     * @param   ChartTermFactoryInterface   $chartTermFactory   ChartTermFactory.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        ChartTermFactoryInterface $chartTermFactory
    );

    /**
     * Find ChartTermEntity from provisioned phase.
     * @param   EntityId    $id     The id of ChartTermEntity.
     * @return  ChartTermEntity     When provisioned ChartTermEntity was found.
     *          null                When couldn't find provisioned ChartTermEntity.
     */
    public function findProvision(EntityId $id);

    /**
     * Find ChartTermEntity from released phase.
     * @param   EntityId    $id     The id of ChartTermEntity.
     * @return  ChartTermEntity     When released ChartTermEntity was found.
     *          null                When couldn't find released ChartTermEntity.
     */
    public function findRelease(EntityId $id);

    /**
     * Get ChartTermEntity from provisioned phase.
     * @param   ChartTermBusinessId     $chartTermBusinessId    The business id of ChartTerm.
     * @param   EntityId                $excludeId              The id of ChartTermEntity you want to exclude.
     * @return  ChartTermEntity     When provisioned ChartTerm was found.
     *          null                When couldn't find provisioned ChartTermEntity.
     */
    public function getProvision(ChartTermBusinessId $chartTermBusinessId, EntityId $excludeId = null);

    /**
     * Get ChartTermEntity from released phase.
     * @param   ChartTermBusinessId     $chartTermBusinessId    The business id of ChartTerm.
     * @param   EntityId                $excludeId              The id of ChartTermEntity you want to exclude.
     * @return  ChartTermEntity     When released ChartTerm was found.
     *          null                When couldn't find released ChartTermEntity.
     */
    public function getRelease(ChartTermBusinessId $chartTermBusinessId, EntityId $excludeId = null);

    /**
     * Find ChartTermAggregation from provisioned phase.
     * @param   EntityId    $id     The id of ChartTermAggregation.
     * @return  ChartTermAggregation    When provisioned ChartTermAggregation was found.
     *          null                    When couldn't find provisioned ChartTermAggregation.
     */
    public function findAggregationProvision(EntityId $id);

    /**
     * Find ChartTermAggregation from released phase.
     * @param   EntityId    $id     The id of ChartTermAggregation.
     * @return  ChartTermAggregation    When released ChartTermAggregation was found.
     *          null                    When couldn't find released ChartTermAggregation.
     */
    public function findAggregationRelease(EntityId $id);

    /**
     * Get ChartTermAggregation from provisioned phase.
     * @param   ChartTermBusinessId     $chartTermBusinessId    The business id of ChartTerm.
     * @return  ChartTermAggregation    When provisioned ChartTermAggregation was found.
     *          null                    When couldn't find provisioned ChartTermAggregation.
     */
    public function getAggregationProvision(ChartTermBusinessId $chartTermBusinessId);

    /**
     * Get ChartTermAggregation from released phase.
     * @param   ChartTermBusinessId     $chartTermBusinessId    The business id of ChartTerm.
     * @return  ChartTermAggregation    When released ChartTermAggregation was found.
     *          null                    When couldn't find released ChartTermAggregation.
     */
    public function getAggregationRelease(ChartTermBusinessId $chartTermBusinessId);

    /**
     * Get ChartTermAggregation from cache and storage.
     * @param   ChartTermBusinessId     $chartTermBusinessId        The business id of ChartTerm.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  ChartTermAggregation    When released ChartTermAggregation was found.
     *          null                    When couldn't find released ChartTermAggregation.
     */
    public function getAggregationWithCache(ChartTermBusinessId $chartTermBusinessId, ChartTermSpecification $chartTermSpecification);

    /**
     * Refresh cached ChartTermAggregation.
     * @param   EntityId                $entityId                   The id of ChartTerm that will be stored to cache.
     * @param   ChartTermBusinessId     $chartTermBusinessId        The business id of ChartTerm that will be deleted from cache.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     */
    public function refreshCachedAggregation(EntityId $entityId, ChartTermBusinessId $chartTermBusinessId, ChartTermSpecification $chartTermSpecification);

    /**
     * Register ChartTermAggregation to provision.
     * @param   ChartTermAggregation    $chartTermAggregation       ChartTermAggregation.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermAggregation was correctly stored.
     *          false   When failed to store ChartTermAggregation.
     * @throws  ChartTermException
     */
    public function register(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification);

    /**
     * Modify ChartTermAggregation of provision.
     * @param   ChartTermAggregation    $chartTermAggregation       ChartTermAggregation.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermAggregation was correctly modified.
     *          false   When failed to modify ChartTermAggregation.
     * @throws  ChartTermException
     */
    public function modifyProvision(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification);

    /**
     * Delete ChartTermAggregation from provision.
     * @param   EntityId                $id                         The id of ChartTermAggregation.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermAggregation was correctly deleted.
     *          false   When failed to delete ChartTermAggregation.
     * @throws  ChartTermException
     */
    public function delete(EntityId $id, ChartTermSpecification $chartTermSpecification);

    /**
     * Release provisioned ChartTermEntity.
     * @param   EntityId                $id                         The id of ChartTermEntity.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermEntity was correctly  released.
     *          false   When failed to release ChartTermEntity.
     * @throws  ChartTermException
     */
    public function release(EntityId $id, ChartTermSpecification $chartTermSpecification);

    /**
     * Modify ChartTermAggregation of release.
     * @param   ChartTermAggregation    $chartTermAggregation       ChartTermAggregation.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermAggregation was correctly modified.
     *          false   When failed to modify ChartTermAggregation.
     * @throws  ChartTermException
     */
    public function modifyRelease(ChartTermAggregation $chartTermAggregation, ChartTermSpecification $chartTermSpecification);

    /**
     * Rollback released ChartTermEntity.
     * @param   EntityId                $id                         The id of ChartTermEntity.
     * @param   ChartTermSpecification  $chartTermSpecification     ChartTermSpecification.
     * @return  true    When ChartTermEntity was correctly  released.
     *          false   When failed to release ChartTermEntity.
     * @throws  ChartTermException
     */
    public function rollback(EntityId $id, ChartTermSpecification $chartTermSpecification);

}
