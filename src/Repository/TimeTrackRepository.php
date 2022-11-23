<?php

namespace App\Repository;

use App\Entity\TimeTrack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeTrack|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeTrack|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeTrack[]    findAll()
 * @method TimeTrack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeTrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeTrack::class);
    }

    // /**
    //  * @return TimeTrack[] Returns an array of TimeTrack objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeTrack
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
