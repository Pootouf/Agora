<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\GameSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSPL>
 *
 * @method GameSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameSPL[]    findAll()
 * @method GameSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class GameSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSPL::class);
    }

}
