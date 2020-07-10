<?php

namespace App\Service;

use App\Entity\Tracking as TrackingEntity;
use App\Helper\TrackingFactory;
use App\Repository\TrackingRepository;
use App\Repository\TruckerRepository;
use App\Repository\TruckTypeRepository;
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

    /**
     * @var TruckTypeRepository
     */
    private $truckTypeRepository;

    /**
     * @param   EntityManagerInterface $entityManager
     * @param   ObjectRepository       $repository
     * @param   EntityFactory          $factory
     * @param   CacheItemPoolInterface $cache
     * @param   TruckerRepository      $truckerRepository
     * @param   TruckTypeRepository    $truckTypeRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TrackingRepository $repository,
        TrackingFactory $factory,
        CacheItemPoolInterface $cache,
        TruckerRepository $truckerRepository,
        TruckTypeRepository $truckTypeRepository
    ) {
        parent::__construct($entityManager, $repository, $factory, $cache);
        $this->truckerRepository = $truckerRepository;
        $this->truckTypeRepository = $truckTypeRepository;
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

    /**
     * @param  array $param
     * @return array
     */
    public function listCheckInByDateRange(array $params): array
    {
        $filters = $params['filters'];

        $isLoaded = isset($filters['is_loaded'])
            ? $filters['is_loaded']
            : true;

        $recentFirst = isset($filters['recent_first'])
            ? $filters['recent_first']
            : true;

        $since = isset($filters['since'])
            ? new \DateTime($filters['since'])
            : null;

        $until = isset($filters['until'])
            ? new \DateTime($filters['until'] . '23:59:59')
            : new \DateTime('now');

        $entityList = $this->getRepository()->findCheckInByDateRange(
            $isLoaded, $recentFirst, $until, $since
        );

        return $entityList;
    }

    /**
     * @param  array $queryString
     * @return array
     */
    public function daysToDateRange(array $queryString): array
    {
        $days = (isset($queryString['filters']['days'])
            && $queryString['filters']['days'] > 0
        )
            ? $queryString['filters']['days'] - 1
            : 0;
        $now = new \DateTime("now");

        $queryString['filters']['until'] = $now->format('Y-m-d');
        $queryString['filters']['since'] = $now
            ->modify('-' . $days . ' days')
            ->format('Y-m-d');

        unset($queryString['filters']['days']);

        return $queryString;
    }

    /**
     * @return  array
     */
    public function listAddressesByTruckType(): array
    {
        $truckTypes = $this->truckTypeRepository->findAll();

        foreach ($truckTypes as $truckType) {
            $result[] = [
                $truckType->getId() => [
                    'truckTypeName' => $truckType->getName(),
                    'trackings' => []
                ]
            ];
        }

        $searchResults = $this->getRepository()->findAll();

        foreach($searchResults as $tracking) {
            $truckTypeId = $tracking->getTrucker()->getTruckType()->getId();

            $result[$truckTypeId]['trackings'][] = [
                'tracking' => [
                    'id' => $tracking->getId(),
                    '_links' => [
                        'rel' => 'self',
                        'path' => '/tracking/' . $tracking->getId()
                    ]
                ],
                'from' => $tracking->getFromAddress(),
                'to' => $tracking->getToAddress(),
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCachePrefix(): string
    {
        return 'tracking_';
    }
}