<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerTileGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerTileGLM>
 *
 * @method PlayerTileGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerTileGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerTileGLM[]    findAll()
 * @method PlayerTileGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerTileGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTileGLM::class);
    }

//    /**
//     * @return PlayerTileGLM[] Returns an array of PlayerTileGLM objects
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

//    public function findOneBySomeField($value): ?PlayerTileGLM
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
