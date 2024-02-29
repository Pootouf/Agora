<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PawnGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PawnGLM>
 *
 * @method PawnGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PawnGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PawnGLM[]    findAll()
 * @method PawnGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PawnGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PawnGLM::class);
    }

//    /**
//     * @return PawnGLM[] Returns an array of PawnGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PawnGLM
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
