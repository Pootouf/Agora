<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\PlayerMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayerMYR>
 *
 * @method PlayerMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerMYR[]    findAll()
 * @method PlayerMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PlayerMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerMYR::class);
    }

}
