<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileBuyCostGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileBuyCostGLM>
 *
 * @method TileBuyCostGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileBuyCostGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileBuyCostGLM[]    findAll()
 * @method TileBuyCostGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TileBuyCostGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileBuyCostGLM::class);
    }

//    /**
//     * @return TileBuyCostGLM[] Returns an array of TileBuyCostGLM objects
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

//    public function findOneBySomeField($value): ?TileBuyCostGLM
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
