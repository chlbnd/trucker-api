<?php

namespace App\Helper;

use App\Helper\EntityFactory;
use App\Entity\Trucker as TruckerEntity;
use App\Repository\TruckTypeRepository;

class TruckerFactory implements EntityFactory
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
     * @return TruckerEntity|null
     */
    public function create(string $json): ?TruckerEntity
    {
        $newTruckerData = json_decode($json);
        $truckType = $this->repository->find(
            $newTruckerData->truck_type_id
        );

        $trucker = new TruckerEntity();
        $trucker
            ->setName($newTruckerData->name)
            ->setBirthdate($newTruckerData->birthdate)
            ->setGender($newTruckerData->gender)
            ->setIsOwner($newTruckerData->is_owner)
            ->setCnhType($newTruckerData->cnh_type)
            ->setIsLoaded($newTruckerData->is_loaded)
            ->setTruckType($truckType);

        return $trucker;
    }
}