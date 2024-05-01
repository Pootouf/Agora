<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\TileMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileMYR>
 *
 * @method TileMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileMYR[]    findAll()
 * @method TileMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TileMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileMYR::class);
    }

}
