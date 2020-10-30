<?php

namespace App\Repository;

use App\Entity\ChatroomMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatroomMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatroomMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatroomMessage[]    findAll()
 * @method ChatroomMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatroomMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatroomMessage::class);
    }

     /**
      * @return ChatroomMessage[] Returns an array of ChatroomMessage objects
      */

    public function getAllMessages($date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.creationDate > :val')
            ->setParameter('val', $date)
            ->orderBy('c.creationDate', 'ASC')
            ->setMaxResults(500)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?ChatroomMessage
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
