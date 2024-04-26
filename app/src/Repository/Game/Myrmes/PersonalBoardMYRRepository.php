<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PersonalBoardMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardMYR>
 *
 * @method PersonalBoardMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardMYR[]    findAll()
 * @method PersonalBoardMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PersonalBoardMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardMYR::class);
    }

}
