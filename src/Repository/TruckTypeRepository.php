<?php

namespace App\Repository;

use App\Entity\TruckType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TruckType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TruckType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TruckType[]    findAll()
 * @method TruckType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TruckTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TruckType::class);
    }
}
