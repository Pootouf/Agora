<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\MainBoardMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoardMYR>
 *
 * @method MainBoardMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoardMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoardMYR[]    findAll()
 * @method MainBoardMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainBoardMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoardMYR::class);
    }

    //    /**
    //     * @return MainBoardMYR[] Returns an array of MainBoardMYR objects
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

    //    public function findOneBySomeField($value): ?MainBoardMYR
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
