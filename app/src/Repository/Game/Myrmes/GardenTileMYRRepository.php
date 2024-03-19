<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\GardenTileMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GardenTileMYR>
 *
 * @method GardenTileMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method GardenTileMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method GardenTileMYR[]    findAll()
 * @method GardenTileMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GardenTileMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GardenTileMYR::class);
    }

    //    /**
    //     * @return GardenTileMYR[] Returns an array of GardenTileMYR objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GardenTileMYR
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
