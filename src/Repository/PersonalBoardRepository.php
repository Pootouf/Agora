<?php

namespace App\Repository;

use App\Entity\PersonalBoard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoard>
 *
 * @method PersonalBoard|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoard|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoard[]    findAll()
 * @method PersonalBoard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalBoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoard::class);
    }

//    /**
//     * @return PersonalBoard[] Returns an array of PersonalBoard objects
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

//    public function findOneBySomeField($value): ?PersonalBoard
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
