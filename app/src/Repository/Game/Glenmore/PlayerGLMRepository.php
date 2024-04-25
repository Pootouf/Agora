<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerGLM>
 *
 * @method PlayerGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerGLM[]    findAll()
 * @method PlayerGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerGLM::class);
    }

}
