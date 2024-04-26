<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\TileTypeMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileTypeMYR>
 *
 * @method TileTypeMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileTypeMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileTypeMYR[]    findAll()
 * @method TileTypeMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TileTypeMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileTypeMYR::class);
    }

}
