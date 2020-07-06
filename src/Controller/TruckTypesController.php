<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\TruckType as TruckTypeEntity;
use App\Helper\TruckTypeFactory;
use App\Helper\RequestSplitter;
use App\Repository\TruckTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

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
        RequestSplitter $requestSplitter
    ) {
        parent::__construct(
            $entityManager, $repository, $factory, $requestSplitter
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
        if(is_null($newData->getPublisher())) {
            throw new \InvalidArgumentException;
        }

        $current->setName($newData->getName());

        return $current;
    }
}
