<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\PlayerSixQP;
use App\Repository\Game\PlayerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerSixQP>
 *
 * @method PlayerSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerSixQP[]    findAll()
 * @method PlayerSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerSixQPRepository extends PlayerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerSixQP::class);
    }

}
