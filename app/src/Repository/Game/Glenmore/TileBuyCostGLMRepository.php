<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\TileBuyCostGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TileBuyCostGLM>
 *
 * @method TileBuyCostGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileBuyCostGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileBuyCostGLM[]    findAll()
 * @method TileBuyCostGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TileBuyCostGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileBuyCostGLM::class);
    }

}
