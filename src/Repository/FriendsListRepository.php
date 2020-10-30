<?php

namespace App\Repository;

use App\Entity\FriendsList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FriendsList|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendsList|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendsList[]    findAll()
 * @method FriendsList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendsListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FriendsList::class);
    }

     /**
      * @return FriendsList[] Returns an array of FriendsList objects
      */

    public function getFriendsOnline($footballer)
    {
        return $this->createQueryBuilder('f')
            ->join('f.footballer', 'u')
            ->andWhere('f.footballer = :footballer')
            ->setParameter('footballer', $footballer->getId())
            ->andWhere('f.accept = 1')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?FriendsList
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
