<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\DiscardSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiscardSixQP>
 *
 * @method DiscardSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscardSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscardSixQP[]    findAll()
 * @method DiscardSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class DiscardSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscardSixQP::class);
    }

}
