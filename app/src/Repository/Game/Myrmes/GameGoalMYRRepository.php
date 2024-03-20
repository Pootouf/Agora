<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\GameGoalMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameGoalMYR>
 *
 * @method GameGoalMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameGoalMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameGoalMYR[]    findAll()
 * @method GameGoalMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameGoalMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameGoalMYR::class);
    }

    //    /**
    //     * @return GameGoalMYR[] Returns an array of GameGoalMYR objects
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

    //    public function findOneBySomeField($value): ?GameGoalMYR
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
