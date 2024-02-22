<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevelopmentCardsSPL>
 *
 * @method DevelopmentCardsSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevelopmentCardsSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevelopmentCardsSPL[]    findAll()
 * @method DevelopmentCardsSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevelopmentCardsSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevelopmentCardsSPL::class);
    }

//    /**
//     * @return DevelopmentCardsSPL[] Returns an array of DevelopmentCardsSPL objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DevelopmentCardsSPL
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
