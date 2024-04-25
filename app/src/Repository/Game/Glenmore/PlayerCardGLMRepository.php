<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PlayerCardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerCardGLM>
 *
 * @method PlayerCardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerCardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerCardGLM[]    findAll()
 * @method PlayerCardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerCardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerCardGLM::class);
    }

}
