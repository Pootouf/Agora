<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\MainBoardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoardSPL>
 *
 * @method MainBoardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoardSPL[]    findAll()
 * @method MainBoardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class MainBoardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoardSPL::class);
    }

}
