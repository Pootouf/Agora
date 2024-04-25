<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PheromonTileMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PheromonTileMYR>
 *
 * @method PheromonTileMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PheromonTileMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PheromonTileMYR[]    findAll()
 * @method PheromonTileMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PheromonTileMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PheromonTileMYR::class);
    }

}
