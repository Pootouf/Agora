<?php

namespace App\Repository\Game;

use App\Entity\Game\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }


   /**
    * @return Message[] Returns an array of Message objects
    */
   public function findByGame(int $gameId): array
   {
       return $this->createQueryBuilder('m')
           ->andWhere('m.gameId = :game')
           ->setParameter('game', $gameId)
           ->orderBy('m.date', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

}
