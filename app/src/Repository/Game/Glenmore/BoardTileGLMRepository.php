<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\BoardTileGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoardTileGLM>
 *
 * @method BoardTileGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardTileGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardTileGLM[]    findAll()
 * @method BoardTileGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class BoardTileGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardTileGLM::class);
    }

}
