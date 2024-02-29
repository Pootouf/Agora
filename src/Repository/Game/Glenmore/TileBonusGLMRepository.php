<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileBonusGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileBonusGLM>
 *
 * @method TileBonusGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileBonusGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileBonusGLM[]    findAll()
 * @method TileBonusGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileBonusGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileBonusGLM::class);
    }

//    /**
//     * @return TileBonusGLM[] Returns an array of TileBonusGLM objects
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

//    public function findOneBySomeField($value): ?TileBonusGLM
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
