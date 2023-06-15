<?php

namespace App\Domain;

interface DomainRepositoryInterface
{

    /**
     * Create the id of Entity.
     * @return  EntityId
     * @throws  DomainException
     */
    public function createId();

    /**
     * Get cache key of Entity.
     * @param   BusinessIdInterface     $businessId     The id depending on business.
     * @param   string                  $entityName     Namespace of Entity class.
     * @return  string  Cache key of Entity.
     */
    public function cacheKey(BusinessIdInterface $businessId, string $entityName);

    /**
     * Store Entity to cache.
     * @param   Entity  $entity         Entity will be cached.
     * @param   string  $entityName     Namespace of Entity class.
     * @return  true    When Entity was correctly cached.
     *          false   When failed to store to cache.
     */
    public function storeCache(Entity $entity, string $entityName);

    /**
     * Store Entity to cache by id.
     * @param   Entity  $entity         Entity will be cached.
     * @param   string  $entityName     Namespace of Entity class.
     * @return  true    When Entity was correctly cached.
     *          false   When failed to store to cache.
     */
    public function storeCacheById(Entity $entity, string $entityName);

    /**
     * Find Entity from cache.
     * @param   BusinessIdInterface     $businessId     The id depending on business.
     * @param   string                  $entityName     Namespace of Entity class.
     * @return  Entity  When Entity is cached.
     *          null    When Entity isn't cached.
     */
    public function findCache(BusinessIdInterface $businessId, string $entityName);

    /**
     * Find Entity from cache by id.
     * @param   EntityId    $entityId       The id of Entity.
     * @param   string      $entityName     Namespace of Entity class.
     * @return  Entity  When Entity is cached.
     *          null    When Entity isn't cached.
     */
    public function findCacheById(EntityId $entityId, string $entityName);

    /**
     * Delete Entity from cache.
     * @param   BusinessIdInterface     $businessId     The id depending on business.
     * @param   string                  $entityName     Namespace of Entity class.
     * @return  int     Count of deleted Entity.
     */
    public function deleteCache(BusinessIdInterface $businessId, string $entityName);

    /**
     * Delete Entity from cache by id.
     * @param   EntityId    $entityId       The id of Entity.
     * @param   string      $entityName     Namespace of Entity class.
     * @return  int     Count of deleted Entity.
     */
    public function deleteCacheById(EntityId $entityId, string $entityName);

}
