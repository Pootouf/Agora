<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\NurseMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NurseMYR>
 *
 * @method NurseMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method NurseMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method NurseMYR[]    findAll()
 * @method NurseMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class NurseMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NurseMYR::class);
    }

}
