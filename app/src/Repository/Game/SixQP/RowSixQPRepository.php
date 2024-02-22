<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\RowSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RowSixQP>
 *
 * @method RowSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method RowSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method RowSixQP[]    findAll()
 * @method RowSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RowSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RowSixQP::class);
    }

//    /**
//     * @return RowSixQP[] Returns an array of RowSixQP objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RowSixQP
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
