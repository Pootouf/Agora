<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerTileResourceGLM>
 *
 * @method PlayerTileResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerTileResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerTileResourceGLM[]    findAll()
 * @method PlayerTileResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerTileResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTileResourceGLM::class);
    }

    //    /**
    //     * @return PlayerTileResourceGLM[] Returns an array of PlayerTileResourceGLM objects
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

    //    public function findOneBySomeField($value): ?PlayerTileResourceGLM
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
