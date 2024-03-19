<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PersonalBoardMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardMYR>
 *
 * @method PersonalBoardMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardMYR[]    findAll()
 * @method PersonalBoardMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalBoardMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardMYR::class);
    }

    //    /**
    //     * @return PersonalBoardMYR[] Returns an array of PersonalBoardMYR objects
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

    //    public function findOneBySomeField($value): ?PersonalBoardMYR
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
