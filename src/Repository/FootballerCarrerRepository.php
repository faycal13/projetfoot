<?php

namespace App\Repository;

use App\Entity\FootballerCarrer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FootballerCarrer|null find($id, $lockMode = null, $lockVersion = null)
 * @method FootballerCarrer|null findOneBy(array $criteria, array $orderBy = null)
 * @method FootballerCarrer[]    findAll()
 * @method FootballerCarrer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FootballerCarrerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FootballerCarrer::class);
    }

    // /**
    //  * @return FootballerCarrer[] Returns an array of FootballerCarrer objects
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
    public function findOneBySomeField($value): ?FootballerCarrer
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
