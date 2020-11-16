<?php

namespace App\Repository;

use App\Entity\BlockFriendsList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlockFriendsList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockFriendsList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockFriendsList[]    findAll()
 * @method BlockFriendsList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockFriendsListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockFriendsList::class);
    }

    // /**
    //  * @return BlockFriendsList[] Returns an array of BlockFriendsList objects
    //  */

    public function getBlockedFootballer($footballer_current, $footballer)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.footballer = :val and b.target = :val2')
            ->orWhere('b.footballer = :val2 and b.target = :val')
            ->setParameter('val', $footballer_current->getId())
            ->setParameter('val2', $footballer->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllBlockedFootballer($footballer)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.footballer = :val')
            ->orWhere('b.target = :val')
            ->setParameter('val', $footballer->getId())
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?BlockFriendsList
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
