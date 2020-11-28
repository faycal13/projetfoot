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

    public function getParticipants($user)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.user = :val')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }
    public function getOthersParticipants($user, $conversation)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.user != :val')
            ->setParameter('val', $user->getId())
            ->andWhere('p.conversation = :conv')
            ->setParameter('conv', $conversation->getId())
            ->getQuery()
            ->getResult()
            ;
    }

    public function getMyParticipation($user, $conversation)
    {
        return $this->createQueryBuilder('p')
            ->join('p.conversation', 'c')
            ->andWhere('p.user = :val')
            ->setParameter('val', $user->getId())
            ->andWhere('p.conversation = :conv')
            ->setParameter('conv', $conversation)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function searchParticipation($user, $user_target)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :val')
            ->setParameter('val', $user->getId())
            ->andWhere('p.participants LIKE :participant1')
            ->setParameter('participant1', '%'.'['.$user->getId().']'.'%')
            ->andWhere('p.participants LIKE :participant2')
            ->setParameter('participant2', '%'.'['.$user_target->getId().']'.'%')
            ->getQuery()
            ->getResult()
            ;
    }
}
