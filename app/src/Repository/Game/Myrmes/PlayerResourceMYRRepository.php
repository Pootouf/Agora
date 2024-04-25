<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PlayerResourceMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerResourceMYR>
 *
 * @method PlayerResourceMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerResourceMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerResourceMYR[]    findAll()
 * @method PlayerResourceMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerResourceMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerResourceMYR::class);
    }

}
