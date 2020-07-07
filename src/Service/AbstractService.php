<?php

namespace App\Service;

use App\Helper\EntityFactory;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Psr\Cache\CacheItemPoolInterface;

abstract class AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var EntityFactory
     */
    private $factory;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(
        EntityManagerInterface $entityManager,
        ObjectRepository $repository,
        EntityFactory $factory,
        CacheItemPoolInterface $cache
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->cache = $cache;
    }

    /**
     * @param  string $entityData
     * @return EntityInterface
     * @throws \Exception
     */
    public function createEntity($entityData): EntityInterface
    {
        $entity = $this->factory->create($entityData);

        if (!$entity) {
            throw new \Exception();
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->saveCacheItem($entity);

        return $entity;
    }

    /**
     * @param  int $id
     * @return EntityInterface|null
     */
    public function getEntity(int $id): ?EntityInterface
    {
        $entity = $this->cache->hasItem($this->getCachePrefix() . $id)
            ? $this->cache->getItem($this->getCachePrefix() . $id)->get()
            : $this->repository->find($id);

        return $entity;
    }

    /**
     * @param  array $params
     * @param  int   $offset
     * @return array|null
     */
    public function getEntityList(array $params, int $offset): ?array
    {
        $entityList = $this->repository->findBy(
            $params['filters'],
            $params['sort'],
            $params['itemsPerPage'],
            $offset
        );

        return $entityList;
    }

    /**
     * @param  string $updateDate
     * @param  int    $id
     * @return EntityInterface
     */
    public function entityUpdate($updateData, int $id): EntityInterface
    {
        $entity = $this->findEntity($id);

        $newEntity = $this->factory->create($updateData);
        $updatedEntity = $this->updateEntity($entity, $newEntity);

        $this->entityManager->flush();
        $this->saveCacheItem($updatedEntity);

        return $updatedEntity;
    }

    /**
     * @param  int $id
     * @return void
     */
    public function deleteEntity(int $id): void
    {
        $entity = $this->findEntity($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->cache->deleteItem($this->getCachePrefix() . $id);
    }

    /**
     * @param  int $id
     * @return EntityInterface
     */
    private function findEntity(int $id): EntityInterface
    {
        $entity = $this->repository->find($id);

        if(is_null($entity)) {
            throw new \Exception;
        }

        return $entity;
    }

    /**
     * @param  EntityInterface $entity
     * @return void
     */
    private function saveCacheItem($entity): void
    {
        $cacheItem = $this->cache->getItem(
            $this->getCachePrefix() . $entity->getId()
        );

        $cacheItem->set($entity);
        $this->cache->save($cacheItem);
    }
}