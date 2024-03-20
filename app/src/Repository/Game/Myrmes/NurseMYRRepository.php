<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\NurseMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NurseMYR>
 *
 * @method NurseMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method NurseMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method NurseMYR[]    findAll()
 * @method NurseMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NurseMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NurseMYR::class);
    }

//    /**
//     * @return NurseMYR[] Returns an array of NurseMYR objects
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

//    public function findOneBySomeField($value): ?NurseMYR
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
