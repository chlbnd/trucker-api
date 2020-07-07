<?php

namespace App\Service;

use App\Entity\TruckType as TruckTypeEntity;
use App\Helper\TruckTypeFactory;
use App\Repository\TruckTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TruckTypeService extends AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TruckTypeRepository
     */
    private $repository;

    /**
     * @var TruckTypeFactory
     */
    private $factory;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(
        EntityManagerInterface $entityManager,
        TruckTypeRepository $repository,
        TruckTypeFactory $factory,
        CacheItemPoolInterface $cache
    ) {
        parent::__construct($entityManager, $repository, $factory, $cache);
    }

    /**
     * @param  TruckTypeEntity   $current
     * @param  TruckTypeEntity   $newData
     * @return TruckTypeEntity
     */
    public function updateEntity(
        TruckTypeEntity $current,
        TruckTypeEntity $newData
    ): TruckTypeEntity
    {
        $current->setName($newData->getName());
        return $current;
    }

    public function getCachePrefix(): string
    {
        return 'trucktype_';
    }
}