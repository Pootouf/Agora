<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\SeasonMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeasonMYR>
 *
 * @method SeasonMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeasonMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeasonMYR[]    findAll()
 * @method SeasonMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class SeasonMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonMYR::class);
    }

}
