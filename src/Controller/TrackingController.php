<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Tracking as TrackingEntity;
use App\Helper\RequestSplitter;
use App\Helper\TrackingFactory;
use App\Repository\TrackingRepository;
use App\Repository\TruckerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TrackingController extends BaseController
{
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TrackingRepository $repository,
        TrackingFactory $factory,
        RequestSplitter $requestSplitter,
        CacheItemPoolInterface $cache,
        TruckerRepository $truckerRepository
    ) {
        parent::__construct(
            $entityManager, $repository, $factory, $requestSplitter, $cache
        );
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
            ->setFromLat($newData->getFromLat())
            ->setFromLon($newData->getFromLon())
            ->setToLat($newData->getToLat())
            ->setToLon($newData->getToLon())
            ->setCheckIn($newData->getCheckIn())
            ->setCheckOut($newData->getCheckOut());

        return $entity;
    }

    public function getCachePrefix(): string
    {
        return 'tracking_';
    }
}
