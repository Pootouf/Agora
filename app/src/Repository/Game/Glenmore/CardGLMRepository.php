<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\CardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardGLM>
 *
 * @method CardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardGLM[]    findAll()
 * @method CardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class CardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardGLM::class);
    }

}
