<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\WarehouseGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarehouseGLM>
 *
 * @method WarehouseGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseGLM[]    findAll()
 * @method WarehouseGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class WarehouseGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseGLM::class);
    }

//    /**
//     * @return WarehouseGLM[] Returns an array of WarehouseGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WarehouseGLM
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
