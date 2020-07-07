<?php

namespace App\Helper;

use App\Entity\EntityInterface;
use Psr\Cache\CacheItemPoolInterface;

class Cache
{
    /**
     * @param  EntityInterface $entity
     * @return void
     */
    public function getCacheItem(string $itemName)
    {
        $cache = new CacheItemPoolInterface();
        return $cache->getItem($itemName)->get();
    }

    /**
     * @param  EntityInterface $entity
     * @return void
     */
    public function saveCacheItem(EntityInterface $entity): void
    {
        $cache = new CacheItemPoolInterface();

        $cacheItem = $cache->getItem(
            $this->getCachePrefix() . $entity->getId()
        );

        $cacheItem->set($entity);
        $cache->save($cacheItem);
    }

    /**
     * @param  string $itemName
     * @return void
     */
    public function deleteItem(string $itemName): void
    {
        $cache = new CacheItemPoolInterface();
        $cache->deleteItem($itemName);
    }
}