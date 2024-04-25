<?php

namespace App\Repository\Game\SixQP;

use App\Entity\Game\SixQP\ChosenCardSixQP;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChosenCardSixQP>
 *
 * @method ChosenCardSixQP|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChosenCardSixQP|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChosenCardSixQP[]    findAll()
 * @method ChosenCardSixQP[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class ChosenCardSixQPRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChosenCardSixQP::class);
    }

}
