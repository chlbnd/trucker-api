<?php

namespace App\Helper;

use App\Entity\TruckType;
use App\Repository\TruckTypeRepository;
use App\Helper\EntityFactory;

class TruckTypeFactory implements EntityFactory
{
    /**
     * @var TruckTypeRepository
     */
    private $repository;

    /**
     * @var TruckTypeRepository $repository
     */
    public function __construct(TruckTypeRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param  string $json
     * @return TruckType
     */
    public function create(string $json)
    {
        $newTruckTypeData = json_decode($json);

        $truckType = new TruckType();
        $truckType->setName($newTruckerData->name);

        return $truckType;
    }
}