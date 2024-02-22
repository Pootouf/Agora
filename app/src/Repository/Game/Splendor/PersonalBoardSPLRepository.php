<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\PersonalBoardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardSPL>
 *
 * @method PersonalBoardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardSPL[]    findAll()
 * @method PersonalBoardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalBoardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardSPL::class);
    }

//    /**
//     * @return PersonalBoardSPL[] Returns an array of PersonalBoardSPL objects
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

//    public function findOneBySomeField($value): ?PersonalBoardSPL
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
