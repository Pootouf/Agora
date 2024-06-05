<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\GoalMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoalMYR>
 *
 * @method GoalMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoalMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoalMYR[]    findAll()
 * @method GoalMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class GoalMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoalMYR::class);
    }

}
