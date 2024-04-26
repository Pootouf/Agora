<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\DrawCardsSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DrawCardsSPL>
 *
 * @method DrawCardsSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrawCardsSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrawCardsSPL[]    findAll()
 * @method DrawCardsSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class DrawCardsSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrawCardsSPL::class);
    }

}
