<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\SeasonMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeasonMYR>
 *
 * @method SeasonMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeasonMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeasonMYR[]    findAll()
 * @method SeasonMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonMYR::class);
    }

    //    /**
    //     * @return SeasonMYR[] Returns an array of SeasonMYR objects
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

    //    public function findOneBySomeField($value): ?SeasonMYR
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
