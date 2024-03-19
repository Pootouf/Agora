<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PreyMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreyMYR>
 *
 * @method PreyMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreyMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreyMYR[]    findAll()
 * @method PreyMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreyMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreyMYR::class);
    }

    //    /**
    //     * @return PreyMYR[] Returns an array of PreyMYR objects
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

    //    public function findOneBySomeField($value): ?PreyMYR
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
