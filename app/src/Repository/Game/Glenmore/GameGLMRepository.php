<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\GameGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameGLM>
 *
 * @method GameGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameGLM[]    findAll()
 * @method GameGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class GameGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameGLM::class);
    }

}
