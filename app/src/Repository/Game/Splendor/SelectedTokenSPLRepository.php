<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\SelectedTokenSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SelectedTokenSPL>
 *
 * @method SelectedTokenSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelectedTokenSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelectedTokenSPL[]    findAll()
 * @method SelectedTokenSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class SelectedTokenSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelectedTokenSPL::class);
    }

}
