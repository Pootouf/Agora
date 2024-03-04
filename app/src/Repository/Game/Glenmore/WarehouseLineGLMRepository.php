<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\WarehouseLineGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarehouseLineGLM>
 *
 * @method WarehouseLineGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseLineGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseLineGLM[]    findAll()
 * @method WarehouseLineGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehouseLineGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseLineGLM::class);
    }

//    /**
//     * @return WarehouseLineGLM[] Returns an array of WarehouseLineGLM objects
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

//    public function findOneBySomeField($value): ?WarehouseLineGLM
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
