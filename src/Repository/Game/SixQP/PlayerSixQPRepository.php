<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\PlayerSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerSixQP>
 *
 * @method PlayerSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerSixQP[]    findAll()
 * @method PlayerSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerSixQP::class);
    }

//    /**
//     * @return PlayerSixQP[] Returns an array of PlayerSixQP objects
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

//    public function findOneBySomeField($value): ?PlayerSixQP
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
