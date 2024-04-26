<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileBuyBonusGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileBuyBonusGLM>
 *
 * @method TileBuyBonusGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileBuyBonusGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileBuyBonusGLM[]    findAll()
 * @method TileBuyBonusGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TileBuyBonusGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileBuyBonusGLM::class);
    }

}
