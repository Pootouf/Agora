<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\CardCostSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardCostSPL>
 *
 * @method CardCostSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardCostSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardCostSPL[]    findAll()
 * @method CardCostSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class CardCostSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardCostSPL::class);
    }

//    /**
//     * @return CardCostSPL[] Returns an array of CardCostSPL objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CardCostSPL
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
