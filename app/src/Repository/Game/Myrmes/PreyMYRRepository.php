<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PreyMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PreyMYR>
 *
 * @method PreyMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PreyMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PreyMYR[]    findAll()
 * @method PreyMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PreyMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PreyMYR::class);
    }

}
