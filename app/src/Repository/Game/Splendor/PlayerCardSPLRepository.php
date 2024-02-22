<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\PlayerCardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerCardSPL>
 *
 * @method PlayerCardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerCardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerCardSPL[]    findAll()
 * @method PlayerCardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerCardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerCardSPL::class);
    }

//    /**
//     * @return PlayerCardSPL[] Returns an array of PlayerCardSPL objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlayerCardSPL
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
