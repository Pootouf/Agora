<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PersonalBoardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardGLM>
 *
 * @method PersonalBoardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardGLM[]    findAll()
 * @method PersonalBoardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PersonalBoardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardGLM::class);
    }

}
