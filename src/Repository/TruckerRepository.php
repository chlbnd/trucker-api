<?php

namespace App\Repository;

use App\Entity\Trucker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trucker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trucker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trucker[]    findAll()
 * @method Trucker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TruckerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trucker::class);
    }
}
