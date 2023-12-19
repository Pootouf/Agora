<?php

namespace App\Repository;

use App\Entity\ListOfCards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListOfCards>
 *
 * @method ListOfCards|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListOfCards|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListOfCards[]    findAll()
 * @method ListOfCards[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListOfCardsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListOfCards::class);
    }

//    /**
//     * @return ListOfCards[] Returns an array of ListOfCards objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListOfCards
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
