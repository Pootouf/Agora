<?php

namespace App\Repository\Game\Glenmore;

use App\Entity\Game\Glenmore\PersonalBoardGLM;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonalBoardGLM>
 *
 * @method PersonalBoardGLM|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalBoardGLM|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalBoardGLM[]    findAll()
 * @method PersonalBoardGLM[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalBoardGLMRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalBoardGLM::class);
    }

//    /**
//     * @return PersonalBoardGLM[] Returns an array of PersonalBoardGLM objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PersonalBoardGLM
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
