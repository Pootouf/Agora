<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\PlayerCardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerCardSPL>
 *
 * @method PlayerCardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerCardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerCardSPL[]    findAll()
 * @method PlayerCardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerCardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerCardSPL::class);
    }

}
