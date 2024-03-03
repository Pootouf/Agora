<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\CardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardGLM>
 *
 * @method CardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardGLM[]    findAll()
 * @method CardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardGLM::class);
    }

//    /**
//     * @return CardGLM[] Returns an array of CardGLM objects
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

//    public function findOneBySomeField($value): ?CardGLM
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
