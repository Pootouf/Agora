<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\MainBoardMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainBoardMYR>
 *
 * @method MainBoardMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainBoardMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainBoardMYR[]    findAll()
 * @method MainBoardMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class MainBoardMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainBoardMYR::class);
    }

}
