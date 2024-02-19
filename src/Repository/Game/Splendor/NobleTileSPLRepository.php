<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\NobleTileSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NobleTileSPL>
 *
 * @method NobleTileSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method NobleTileSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method NobleTileSPL[]    findAll()
 * @method NobleTileSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class NobleTileSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NobleTileSPL::class);
    }

//    /**
//     * @return NobleTileSPL[] Returns an array of NobleTileSPL objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NobleTileSPL
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
