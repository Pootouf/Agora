<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\NobleTileSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NobleTileSPL>
 *
 * @method NobleTileSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method NobleTileSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method NobleTileSPL[]    findAll()
 * @method NobleTileSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class NobleTileSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NobleTileSPL::class);
    }

}
