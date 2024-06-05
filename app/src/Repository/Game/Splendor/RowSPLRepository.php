<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\RowSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RowSPL>
 *
 * @method RowSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method RowSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method RowSPL[]    findAll()
 * @method RowSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class RowSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RowSPL::class);
    }

}
