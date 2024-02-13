<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\MainBoardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoardSPL>
 *
 * @method MainBoardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoardSPL[]    findAll()
 * @method MainBoardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainBoardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoardSPL::class);
    }

//    /**
//     * @return MainBoardSPL[] Returns an array of MainBoardSPL objects
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

//    public function findOneBySomeField($value): ?MainBoardSPL
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
