<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\CreatedResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreatedResourceGLM>
 *
 * @method CreatedResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreatedResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreatedResourceGLM[]    findAll()
 * @method CreatedResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreatedResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreatedResourceGLM::class);
    }

    //    /**
    //     * @return CreatedResourceGLM[] Returns an array of CreatedResourceGLM objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CreatedResourceGLM
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
