<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\SelectedResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SelectedResourceGLM>
 *
 * @method SelectedResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelectedResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelectedResourceGLM[]    findAll()
 * @method SelectedResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class SelectedResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelectedResourceGLM::class);
    }

    //    /**
    //     * @return SelectedResourceGLM[] Returns an array of SelectedResourceGLM objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SelectedResourceGLM
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
