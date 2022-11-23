<?php

namespace App\Repository;

use App\Entity\DateLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DateLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateLog[]    findAll()
 * @method DateLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateLog::class);
    }

    // /**
    //  * @return DateLog[] Returns an array of DateLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DateLog
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
