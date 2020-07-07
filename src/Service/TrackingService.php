<?php

namespace App\Service;

use App\Entity\Tracking as TrackingEntity;
use App\Helper\TrackingFactory;
use App\Repository\TrackingRepository;
use App\Repository\TruckerRepository;
use App\Service\TruckerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TrackingService extends AbstractService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TrackingRepository
     */
    private $repository;

    /**
     * @var TrackingFactory
     */
    private $factory;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrackingRepository $repository,
        TrackingFactory $factory,
        CacheItemPoolInterface $cache,
        TruckerRepository $truckerRepository
    ) {
        parent::__construct($entityManager, $repository, $factory, $cache);
        $this->truckerRepository = $truckerRepository;
    }


    /**
     * @param  TrackingEntity   $current
     * @param  TrackingEntity   $newData
     * @return TrackingEntity
     */
    public function updateEntity(
        TrackingEntity $current,
        TrackingEntity $newData
    ): TrackingEntity
    {
        if(is_null($newData->getTrucker())) {
            throw new \InvalidArgumentException;
        }

        $trucker = $this->truckerRepository->find(
            $newData->getTrucker()->getId()
        );

        $current
            ->setTrucker($trucker)
            ->setFromAddress($newData->getFromAddress())
            ->setToAddress($newData->getToAddress())
            ->setCheckIn($newData->getCheckIn())
            ->setCheckOut($newData->getCheckOut());

        return $current;
    }

    public function getCachePrefix(): string
    {
        return 'tracking_';
    }
}