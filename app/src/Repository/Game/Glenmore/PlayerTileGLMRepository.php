<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerTileGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerTileGLM>
 *
 * @method PlayerTileGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerTileGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerTileGLM[]    findAll()
 * @method PlayerTileGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerTileGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerTileGLM::class);
    }

}
