<?php

namespace App\Service;

use App\Entity\Trucker as TruckerEntity;
use App\Helper\TruckerFactory;
use App\Repository\TrackingRepository;
use App\Repository\TruckerRepository;
use App\Repository\TruckTypeRepository;
use App\Service\TrackingService;
use App\Service\TruckTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TruckerService extends AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TruckerRepository
     */
    private $repository;

    /**
     * @var TruckerFactory
     */
    private $factory;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var TruckTypeService
     */
    private $truckTypeRepository;

    /**
     * @var TrackingService
     */
    private $trackingRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TruckerRepository $repository,
        TruckerFactory $factory,
        CacheItemPoolInterface $cache,
        TruckTypeRepository $truckTypeRepository,
        TrackingRepository $trackingRepository
    ) {
        parent::__construct($entityManager, $repository, $factory, $cache);
        $this->truckTypeRepository = $truckTypeRepository;
        $this->trackingRepository = $trackingRepository;
    }

    /**
     * @param  TruckerEntity   $current
     * @param  TruckerEntity   $newData
     * @return TruckerEntity
     */
    public function updateEntity(
        TruckerEntity $current,
        TruckerEntity $newData
    ): TruckerEntity
    {
        if(is_null($newData->getTruckType())) {
            throw new \Exception;
        }

        $truckType = $this->truckTypeRepository->find(
            $newData->getTruckType()->getId()
        );

        $current
            ->setName($newData->getName())
            ->setBirthdate($newData->getBirthdate())
            ->setGender($newData->getGender())
            ->setIsOwner($newData->getIsOwner())
            ->setCnhType($newData->getCnhType())
            ->setIsLoaded($newData->getIsLoaded())
            ->setTruckType($truckType);

        return $current;
    }

    public function getCachePrefix(): string
    {
        return 'trucker_';
    }
}