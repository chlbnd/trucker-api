<?php

namespace App\Repository;

use App\Entity\Tracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracking[]    findAll()
 * @method Tracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracking::class);
    }

    public function findCheckInByDateRange(
        bool $isLoaded,
        bool $recentFirst,
        \DateTime $until,
        \DateTime $since = null
    ) {
        $params = [
            'since'    => $since ?? 0,
            'until'    => $until,
            'isLoaded' => $isLoaded
        ];

        $qb = $this->createQueryBuilder('tracking')
            ->select('tra')
            ->from(Tracking::class, 'tra')
            ->innerJoin('tra.trucker', 'tru', 'tra.trucker_id = tru.id')
            ->andWhere('tra.check_in >= :since')
            ->andWhere('tra.check_in <= :until')
            ->andWhere('tru.is_loaded = :isLoaded')
            ->setParameters($params);

        $recentFirst
            ? $qb->orderBy('tra.check_in', 'DESC')
            : $qb->orderBy('tra.check_in', 'ASC');

        return $qb->getQuery()->execute();
    }
}
