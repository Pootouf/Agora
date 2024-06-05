<?php

namespace App\Repository\Game\Myrmes;

use App\Entity\Game\Myrmes\GardenWorkerMYR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GardenWorkerMYR>
 *
 * @method GardenWorkerMYR|null find($id, $lockMode = null, $lockVersion = null)
 * @method GardenWorkerMYR|null findOneBy(array $criteria, array $orderBy = null)
 * @method GardenWorkerMYR[]    findAll()
 * @method GardenWorkerMYR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * @codeCoverageIgnore
 */
class GardenWorkerMYRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GardenWorkerMYR::class);
    }

}
