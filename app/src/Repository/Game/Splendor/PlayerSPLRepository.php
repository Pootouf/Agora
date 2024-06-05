<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\PlayerSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerSPL>
 *
 * @method PlayerSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerSPL[]    findAll()
 * @method PlayerSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerSPL::class);
    }

}
