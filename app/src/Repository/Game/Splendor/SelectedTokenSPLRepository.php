<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\SelectedTokenSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SelectedTokenSPL>
 *
 * @method SelectedTokenSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelectedTokenSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelectedTokenSPL[]    findAll()
 * @method SelectedTokenSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class SelectedTokenSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelectedTokenSPL::class);
    }

//    /**
//     * @return SelectedTokenSPL[] Returns an array of SelectedTokenSPL objects
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

//    public function findOneBySomeField($value): ?SelectedTokenSPL
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
