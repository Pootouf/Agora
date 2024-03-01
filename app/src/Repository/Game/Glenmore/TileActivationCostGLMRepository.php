<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileActivationCostGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileActivationCostGLM>
 *
 * @method TileActivationCostGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileActivationCostGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileActivationCostGLM[]    findAll()
 * @method TileActivationCostGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileActivationCostGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileActivationCostGLM::class);
    }

    //    /**
    //     * @return TileActivationCostGLM[] Returns an array of TileActivationCostGLM objects
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

    //    public function findOneBySomeField($value): ?TileActivationCostGLM
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
