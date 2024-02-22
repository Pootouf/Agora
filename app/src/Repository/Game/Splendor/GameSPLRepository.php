<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\GameSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSPL>
 *
 * @method GameSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameSPL[]    findAll()
 * @method GameSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSPL::class);
    }

//    /**
//     * @return GameSPL[] Returns an array of GameSPL objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GameSPL
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
