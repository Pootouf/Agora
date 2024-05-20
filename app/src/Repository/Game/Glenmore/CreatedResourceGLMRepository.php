<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\CreatedResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreatedResourceGLM>
 *
 * @method CreatedResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreatedResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreatedResourceGLM[]    findAll()
 * @method CreatedResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class CreatedResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreatedResourceGLM::class);
    }

}
