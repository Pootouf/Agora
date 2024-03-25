<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\DrawTilesGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DrawTilesGLM>
 *
 * @method DrawTilesGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrawTilesGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrawTilesGLM[]    findAll()
 * @method DrawTilesGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class DrawTilesGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrawTilesGLM::class);
    }

//    /**
//     * @return DrawTilesGLM[] Returns an array of DrawTilesGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DrawTilesGLM
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
