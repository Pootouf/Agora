<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillWorkerMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnthillWorkerMYR>
 *
 * @method AnthillWorkerMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnthillWorkerMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnthillWorkerMYR[]    findAll()
 * @method AnthillWorkerMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnthillWorkerMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnthillWorkerMYR::class);
    }

    //    /**
    //     * @return AnthillWorkerMYR[] Returns an array of AnthillWorkerMYR objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AnthillWorkerMYR
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
