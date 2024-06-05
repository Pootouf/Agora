<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\ResourceGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResourceGLM>
 *
 * @method ResourceGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceGLM[]    findAll()
 * @method ResourceGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class ResourceGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceGLM::class);
    }

}
