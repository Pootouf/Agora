<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\GameMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameMYR>
 *
 * @method GameMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameMYR[]    findAll()
 * @method GameMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameMYR::class);
    }

    //    /**
    //     * @return GameMYR[] Returns an array of GameMYR objects
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

    //    public function findOneBySomeField($value): ?GameMYR
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
