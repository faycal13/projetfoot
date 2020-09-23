<?php

namespace App\Repository;

use App\Entity\Footballer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Footballer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Footballer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Footballer[]    findAll()
 * @method Footballer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FootballerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Footballer::class);
    }

    // /**
    //  * @return Footballer[] Returns an array of Footballer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Footballer
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
