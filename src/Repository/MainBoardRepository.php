<?php

namespace App\Repository;

use App\Entity\MainBoard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoard>
 *
 * @method MainBoard|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoard|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoard[]    findAll()
 * @method MainBoard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainBoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoard::class);
    }

//    /**
//     * @return MainBoard[] Returns an array of MainBoard objects
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

//    public function findOneBySomeField($value): ?MainBoard
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
