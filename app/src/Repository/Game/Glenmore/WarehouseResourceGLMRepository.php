<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\WarehouseResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarehouseResourceGLM>
 *
 * @method WarehouseResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseResourceGLM[]    findAll()
 * @method WarehouseResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehouseResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseResourceGLM::class);
    }

    //    /**
    //     * @return WarehouseResourceGLM[] Returns an array of WarehouseResourceGLM objects
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

    //    public function findOneBySomeField($value): ?WarehouseResourceGLM
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
