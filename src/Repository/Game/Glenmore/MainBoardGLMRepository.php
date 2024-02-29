<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\MainBoardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoardGLM>
 *
 * @method MainBoardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoardGLM[]    findAll()
 * @method MainBoardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainBoardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoardGLM::class);
    }

//    /**
//     * @return MainBoardGLM[] Returns an array of MainBoardGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MainBoardGLM
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
