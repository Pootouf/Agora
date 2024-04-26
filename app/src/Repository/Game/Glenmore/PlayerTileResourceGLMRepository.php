<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerTileResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerTileResourceGLM>
 *
 * @method PlayerTileResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerTileResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerTileResourceGLM[]    findAll()
 * @method PlayerTileResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerTileResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTileResourceGLM::class);
    }

}
