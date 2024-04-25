<?php

namespace App\Repository\Game\DTO;

use App\Entity\Game\DTO\Pawn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pawn>
 *
 * @method Pawn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pawn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pawn[]    findAll()
 * @method Pawn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class PawnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pawn::class);
    }

}
