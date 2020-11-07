<?php

namespace App\Repository;

use App\Entity\ParticipantConversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipantConversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipantConversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipantConversation[]    findAll()
 * @method ParticipantConversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipantConversation::class);
    }

    // /**
    //  * @return ParticipantConversation[] Returns an array of ParticipantConversation objects
    //  */

    public function getParticipants($footballer)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.footballer = :val')
            ->setParameter('val', $footballer->getId())
            ->getQuery()
            ->getResult()
        ;
    }
    public function getOthersParticipants($footballer, $conversation)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.footballer != :val')
            ->setParameter('val', $footballer->getId())
            ->andWhere('p.conversation = :conv')
            ->setParameter('conv', $conversation->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function getMyParticipation($footballer, $conversation)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.footballer = :val')
            ->setParameter('val', $footballer->getId())
            ->andWhere('p.conversation = :conv')
            ->setParameter('conv', $conversation)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?ParticipantConversation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
