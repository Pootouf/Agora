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

    // public function findAllMessageFromGame(int $gameId)
    // {
    //     $bdd = $this->getEntityManager()->getConnection();

    //     $request = 'SELECT * FROM `message` WHERE WHERE message.gameId = '
    //         . $gameId . ' ORDER BY message.date';
        
    //     $result = $bdd->executeQuery($request);

    //     return $result->fetchAllAssociative();
    // }

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

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
