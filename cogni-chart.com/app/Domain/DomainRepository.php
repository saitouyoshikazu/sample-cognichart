<?php

namespace App\Domain;
use App\Infrastructure\RedisDAO\RedisDAOInterface;
use App\Infrastructure\BuilderContainerInterface;
use App\Infrastructure\BuilderContainer;

abstract class DomainRepository implements DomainRepositoryInterface
{

    protected $redisDAO;

    public function __construct(RedisDAOInterface $redisDAO)
    {
        $this->redisDAO = $redisDAO;
    }

    public function createId()
    {
        $limitRetry = 10;
        for ($i = 0; $i < $limitRetry; $i++) {
            $idValue = hash("md5", uniqid(rand(), true));
            $entityId = new EntityId($idValue);
            if (!$this->idExisting($entityId)) {
                return $entityId;
            }
        }
        throw new DomainLayerException('Failed to create id of entity.');
    }

    /**
     * Check id is existing.
     * @param   EntityId    $entityId   The id of Entity.
     * @return  true        When id is existing.
     *          false       When id isn't existing.
     */
    abstract protected function idExisting(EntityId $id);

    public function cacheKey(BusinessIdInterface $businessId, string $entityName)
    {
        return $entityName . ':' . $businessId->value();
    }

    public function cacheKeyById(EntityId $entityId, string $entityName)
    {
        return $entityName . ':idBase:' . $entityId->value();
    }

    public function storeCache(Entity $entity, string $entityName)
    {
        return $this->redisDAO->set(
            $this->cacheKey($entity->businessId(), $entityName),
            serialize($entity)
        );
    }

    public function storeCacheById(Entity $entity, string $entityName)
    {
        return $this->redisDAO->set(
            $this->cacheKeyById($entity->id(), $entityName),
            serialize($entity)
        );
    }

    public function findCache(BusinessIdInterface $businessId, string $entityName)
    {
        $cache = $this->redisDAO->get($this->cacheKey($businessId, $entityName));
        if (empty($cache)) {
            return null;
        }
        return unserialize($cache);
    }

    public function findCacheById(EntityId $entityId, string $entityName)
    {
        $cache = $this->redisDAO->get($this->cacheKeyById($entityId, $entityName));
        if (empty($cache)) {
            return null;
        }
        return unserialize($cache);
    }

    public function deleteCache(BusinessIdInterface $businessId, string $entityName)
    {
        return $this->redisDAO->del($this->cacheKey($businessId, $entityName));
    }

    public function deleteCacheById(EntityId $entityId, string $entityName)
    {
        return $this->redisDAO->del($this->cacheKeyById($entityId, $entityName));
    }

    protected function initBuilder(BuilderContainerInterface $builderContainer = null, string $key)
    {
        if (empty($builderContainer)) {
            $builderContainer = new BuilderContainer();
        }
        $builder = $builderContainer->get($key);
        if (empty($builder)) {
            $eloquentNameSpace = "App\\Infrastructure\\Eloquents\\{$key}";
            $builder = $eloquentNameSpace::query();
            $builderContainer->set($key, $builder);
        }
        return $builderContainer;
    }

}
