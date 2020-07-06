<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Trucker as TruckerEntity;
use App\Helper\TruckerFactory;
use App\Helper\RequestSplitter;
use App\Repository\TruckerRepository;
use App\Repository\TruckTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class TruckersController extends BaseController
{
    /**
     * @var TruckTypeRepository
     */
    private $truckTypeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TruckerRepository $repository,
        TruckerFactory $factory,
        TruckTypeRepository $truckTypeRepository,
        RequestSplitter $requestSplitter
    ) {
        parent::__construct(
            $entityManager,
            $repository,
            $factory,
            $requestSplitter
        );
        $this->truckTypeRepository = $truckTypeRepository;
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
            ->setTitle($newData->getTitle())
            ->setAuthor($newData->getAuthor())
            ->setIsbn($newData->getIsbn())
            ->setPublisher($truckType);

        return $entity;
    }
}
