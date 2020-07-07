<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Trucker as TruckerEntity;
use App\Helper\RequestSplitter;
use App\Helper\TruckerFactory;
use App\Repository\TrackingRepository;
use App\Repository\TruckerRepository;
use App\Repository\TruckTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TruckersController extends BaseController
{
    /**
     * @var TruckTypeRepository
     */
    private $truckTypeRepository;
    /**
     * @var TrackingRepository
     */
    private $trackingRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TruckerRepository $repository,
        TruckerFactory $factory,
        RequestSplitter $requestSplitter,
        CacheItemPoolInterface $cache,
        TruckTypeRepository $truckTypeRepository,
        TrackingRepository $trackingRepository
    ) {
        parent::__construct(
            $entityManager, $repository, $factory, $requestSplitter, $cache
        );
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
            throw new \InvalidArgumentException;
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

        return $entity;
    }

    public function getCachePrefix(): string
    {
        return 'trucker_';
    }
}
