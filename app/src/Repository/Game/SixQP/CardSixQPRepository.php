<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\CardSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardSixQP>
 *
 * @method CardSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardSixQP[]    findAll()
 * @method CardSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class CardSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardSixQP::class);
    }

//    /**
//     * @return CardSixQP[] Returns an array of CardSixQP objects
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

//    public function findOneBySomeField($value): ?CardSixQP
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}