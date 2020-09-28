<?php

namespace App\Repository;

use App\Entity\ChatroomList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatroomList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatroomList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatroomList[]    findAll()
 * @method ChatroomList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatroomListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatroomList::class);
    }

    // /**
    //  * @return ChatroomList[] Returns an array of ChatroomList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChatroomList
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
