<?php

namespace App\Repository;

use App\Entity\FootballerPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FootballerPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method FootballerPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method FootballerPhoto[]    findAll()
 * @method FootballerPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FootballerPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FootballerPhoto::class);
    }

    // /**
    //  * @return FootballerPhoto[] Returns an array of FootballerPhoto objects
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
    public function findOneBySomeField($value): ?FootballerPhoto
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
