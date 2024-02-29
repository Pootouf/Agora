<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileCostGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileCostGLM>
 *
 * @method TileCostGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileCostGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileCostGLM[]    findAll()
 * @method TileCostGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileCostGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileCostGLM::class);
    }

//    /**
//     * @return TileCostGLM[] Returns an array of TileCostGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TileCostGLM
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
