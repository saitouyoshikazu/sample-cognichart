<?php

namespace App\Domain\Chart;
use App\Domain\DomainRepositoryInterface;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Domain\ChartTerm\ChartTermListRepositoryInterface;
use App\Domain\ValueObjects\Phase;
use App\Domain\EntityId;

interface ChartRepositoryInterface extends DomainRepositoryInterface
{

    /**
     * Constructor.
     * @param   RedisDAOInterface                   $redisDAO                   RedisDAO.
     * @param   ChartFactoryInterface               $chartFactory               ChartFactory.
     * @param   ChartTermListRepositoryInterface    $chartTermListRepository    ChartTermListRepository.
     */
    public function __construct(
        RedisDAOInterface $redisDAO,
        ChartFactoryInterface $chartFactory,
        ChartTermListRepositoryInterface $chartTermListRepository
    );

    /**
     * Find ChartEntity from provisioned phase.
     * @param   EntityId    $id     The id of ChartEntity.
     * @return  ChartEntity When ChartEntity was found.
     *          null        When couldn't find ChartEntity.
     */
    public function findProvision(EntityId $id);

    /**
     * Find ChartEntity from released phase.
     * @param   EntityId    $id     The id of ChartEntity.
     * @return  ChartEntity When ChartEntity was found.
     *          null        When couldn't find ChartEntity.
     */
    public function findRelease(EntityId $id);

    /**
     * Get ChartEntity from provisioned phase.
     * @param   ChartBusinessId     $chartBusinessId    The business id of Chart.
     * @param   EntityId            $excludeId          The id of ChartEntity you want to exclude.
     * @return  ChartEntity     When provisioned Chart was found.
     *          null            When provisioned Chart wasn't found.
     */
    public function getProvision(ChartBusinessId $chartBusinessId, EntityId $excludeId = null);

    /**
     * Get ChartEntity from released phase.
     * @param   ChartBusinessId     $chartBusinessId    The business id of Chart.
     * @param   EntityId            $excludeId          The id of ChartEntity you want to exclude.
     * @return  ChartEntity     When provisioned Chart was found.
     *          null            When provisioned Chart wasn't found.
     */
    public function getRelease(ChartBusinessId $chartBusinessId, EntityId $excludeId = null);

    /**
     * Find ChartAggregation from provisioned phase.
     * @param   EntityId    $id                 The id of Chart.
     * @param   Phase       $chartTermPhase     The phase of ChartTerm.
     * @return  ChartAggregation    When ChartAggregation was found.
     *          null                When couldn't find ChartAggregation.
     */
    public function findAggregationProvision(EntityId $id, Phase $chartTermPhase);

    /**
     * Find ChartAggregation from released phase.
     * @param   EntityId    $id                 The id of Chart.
     * @param   Phase       $chartTermPhase     The phase of ChartTerm.
     * @return  ChartAggregation    When ChartAggregation was found.
     *          null                When couldn't find ChartAggregation.
     */
    public function findAggregationRelease(EntityId $id, Phase $chartTermPhase);

    /**
     * Get ChartAggregation from provisioned phase.
     * @param   ChartBusinessId     $chartBusinessId    The business id of Chart.
     * @param   Phase               $chartTermPhase     The phase of ChartTerm.
     * @return  ChartAggregation    When ChartAggregation was found.
     *          null                When couldn't find ChartAggregation.
     */
    public function getAggregationProvision(ChartBusinessId $chartBusinessId, Phase $chartTermPhase);

    /**
     * Get ChartAggregation from released phase.
     * @param   ChartBusinessId     $chartBusinessId    The business id of Chart.
     * @param   Phase               $chartTermPhase     The phase of ChartTerm.
     * @return  ChartAggregation    When ChartAggregation was found.
     *          null                When couldn't find ChartAggregation.
     */
    public function getAggregationRelease(ChartBusinessId $chartBusinessId, Phase $chartTermPhase);

    /**
     * Get released ChartAggregation with released ChartTerm from cache and storage.
     * @param   ChartBusinessId     $chartBusinessId        The business id of Chart.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  ChartAggregation    When ChartAggregation was found.
     *          null                When couldn't find ChartAggregation.
     */
    public function getAggregationWithCache(ChartBusinessId $chartBusinessId, ChartSpecification $chartSpecification);

    /**
     * Refresh cached ChartAggregation.
     * @param   EntityId            $entityId               The id of Chart that will be stored to cache.
     * @param   ChartBusinessId     $businessId             The business id of Chart that will be deleted from cache.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     */
    public function refreshCachedAggregation(EntityId $entityId, ChartBusinessId $businessId, ChartSpecification $chartSpecification);

    /**
     * Store ChartEntity to provision.
     * @param   ChartEntity         $chartEntity            ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly stored.
     *          false   When failed to store ChartEntity.
     * @throws  ChartException
     */
    public function register(ChartEntity $chartEntity, ChartSpecification $chartSpecification);

    /**
     * Modify ChartEntity of provision.
     * @param   ChartEntity         $chartEntity            ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly modified.
     *          false   When failed to modify ChartEntity.
     * @throws  ChartException
     */
    public function modifyProvision(ChartEntity $chartEntity, ChartSpecification $chartSpecification);

    /**
     * Delete ChartEntity from provision.
     * @param   EntityId            $id                     The id of ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly deleted.
     *          false   When failed to delete ChartEntity.
     * @throws  ChartException
     */
    public function delete(EntityId $id, ChartSpecification $chartSpecification);

    /**
     * Release provisioned ChartEntity.
     * @param   EntityId            $id                     The id of ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly  released.
     *          false   When failed to release ChartEntity.
     * @throws  ChartException
     */
    public function release(EntityId $id, ChartSpecification $chartSpecification);

    /**
     * Modify ChartEntity of release.
     * @param   ChartEntity         $chartEntity            ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly modified.
     *          false   When failed to modify ChartEntity.
     * @throws  ChartException
     */
    public function modifyRelease(ChartEntity $chartEntity, ChartSpecification $chartSpecification);

    /**
     * Rollback released ChartEntity.
     * @param   EntityId            $id                     The id of ChartEntity.
     * @param   ChartSpecification  $chartSpecification     ChartSpecification.
     * @return  true    When ChartEntity was correctly  released.
     *          false   When failed to release ChartEntity.
     * @throws  ChartException
     */
    public function rollback(EntityId $id, ChartSpecification $chartSpecification);

}
