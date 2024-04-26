<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\PersonalBoardSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardSPL>
 *
 * @method PersonalBoardSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardSPL[]    findAll()
 * @method PersonalBoardSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PersonalBoardSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardSPL::class);
    }

}
