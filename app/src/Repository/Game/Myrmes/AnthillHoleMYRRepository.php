<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\AnthillHoleMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnthillHoleMYR>
 *
 * @method AnthillHoleMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnthillHoleMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnthillHoleMYR[]    findAll()
 * @method AnthillHoleMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class AnthillHoleMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnthillHoleMYR::class);
    }

}
