<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PheromonMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PheromonMYR>
 *
 * @method PheromonMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PheromonMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PheromonMYR[]    findAll()
 * @method PheromonMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PheromonMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PheromonMYR::class);
    }

}
