<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileActivationBonusGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileActivationBonusGLM>
 *
 * @method TileActivationBonusGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileActivationBonusGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileActivationBonusGLM[]    findAll()
 * @method TileActivationBonusGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileActivationBonusGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileActivationBonusGLM::class);
    }

//    /**
//     * @return TileActivationBonusGLM[] Returns an array of TileActivationBonusGLM objects
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

//    public function findOneBySomeField($value): ?TileActivationBonusGLM
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
