<?php

namespace App\Repository;

use App\Entity\FootballerVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FootballerVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FootballerVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FootballerVideo[]    findAll()
 * @method FootballerVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FootballerVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FootballerVideo::class);
    }

    // /**
    //  * @return FootballerVideo[] Returns an array of FootballerVideo objects
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
    public function findOneBySomeField($value): ?FootballerVideo
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
