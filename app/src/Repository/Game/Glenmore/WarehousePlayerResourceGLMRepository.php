<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\WarehousePlayerResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarehousePlayerResourceGLM>
 *
 * @method WarehousePlayerResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehousePlayerResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehousePlayerResourceGLM[]    findAll()
 * @method WarehousePlayerResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehousePlayerResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehousePlayerResourceGLM::class);
    }

    //    /**
    //     * @return WarehousePlayerResourceGLM[] Returns an array of WarehousePlayerResourceGLM objects
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

    //    public function findOneBySomeField($value): ?WarehousePlayerResourceGLM
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
