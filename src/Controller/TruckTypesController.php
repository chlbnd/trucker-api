<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\TruckType as TruckTypeEntity;
use App\Helper\RequestSplitter;
use App\Helper\TruckTypeFactory;
use App\Repository\TruckTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

class TruckTypesController extends BaseController
{
    /**
     * @var TruckerRepository
     */
    private $truckerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TruckTypeRepository $repository,
        TruckTypeFactory $factory,
        RequestSplitter $requestSplitter,
        CacheItemPoolInterface $cache
    ) {
        parent::__construct(
            $entityManager, $repository, $factory, $requestSplitter, $cache
        );
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
