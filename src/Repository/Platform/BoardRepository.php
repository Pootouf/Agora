<?php

namespace App\Repository\Platform;

use App\Entity\Platform\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Board>
 *
 * @method Board|null find($id, $lockMode = null, $lockVersion = null)
 * @method Board|null findOneBy(array $criteria, array $orderBy = null)
 * @method Board[]    findAll()
 * @method Board[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }



//    /**
//     * @return Board[] Returns an array of Board objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Board
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Results of boards linked with search
     * @return Board[]
     */
    public function searchBoards(SearchData $search): array
    {
        $query = $this->createQueryBuilder('b');

        if (!empty($search->status)) {
            $query->andWhere('b.status = :status')
                ->setParameter('status', "{$search->status}");
        }

        if (!empty($search->availability)) {
            if ( $search->availability === 'OPEN') {
                $query->andWhere('b.status != :status')
                    ->setParameter('status', 'IN_GAME')
                    ->andWhere('(count(b.listUsers) + b.nbInvitations) < b.nbUserMax');
            } elseif ($search->availability === 'CLOSE') {
                $query->andWhere('b.status != :status')
                    ->setParameter('status', 'IN_GAME')
                    ->orWhere('(COUNT(b.listUsers)+ b.nbInvitations) >= b.nbUserMax');
            }
        }

        if (!empty($search->datecreation)) {
            $query->andWhere('b.creationDate = :creationdate')
                ->setParameter('creationdate', $search->datecreation);
        }

        if (!empty($search->game)) {
            $query->andWhere('b.game = :game')
                ->setParameter('game', $search->game);
        }

        return $query->getQuery()->getResult();
    }


}
