<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PlayerResourceMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerResourceMYR>
 *
 * @method PlayerResourceMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerResourceMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerResourceMYR[]    findAll()
 * @method PlayerResourceMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerResourceMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerResourceMYR::class);
    }

    //    /**
    //     * @return PlayerResourceMYR[] Returns an array of PlayerResourceMYR objects
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

    //    public function findOneBySomeField($value): ?PlayerResourceMYR
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
