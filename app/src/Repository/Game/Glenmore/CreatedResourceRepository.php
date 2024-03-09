<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\CreatedResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreatedResource>
 *
 * @method CreatedResource|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreatedResource|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreatedResource[]    findAll()
 * @method CreatedResource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreatedResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreatedResource::class);
    }

    //    /**
    //     * @return CreatedResource[] Returns an array of CreatedResource objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CreatedResource
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
