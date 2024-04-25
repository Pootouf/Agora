<?php

namespace App\Repository\Game\Splendor;

use App\Entity\Game\Splendor\TokenSPL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TokenSPL>
 *
 * @method TokenSPL|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenSPL|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenSPL[]    findAll()
 * @method TokenSPL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class TokenSPLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenSPL::class);
    }

}
