<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\RowSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RowSixQP>
 *
 * @method RowSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method RowSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method RowSixQP[]    findAll()
 * @method RowSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class RowSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RowSixQP::class);
    }

}
