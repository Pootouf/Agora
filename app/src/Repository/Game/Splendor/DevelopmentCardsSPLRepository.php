<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\DevelopmentCardsSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevelopmentCardsSPL>
 *
 * @method DevelopmentCardsSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevelopmentCardsSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevelopmentCardsSPL[]    findAll()
 * @method DevelopmentCardsSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class DevelopmentCardsSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevelopmentCardsSPL::class);
    }

}
