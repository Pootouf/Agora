<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\GameSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameSixQP>
 *
 * @method GameSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameSixQP[]    findAll()
 * @method GameSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class GameSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameSixQP::class);
    }

}
