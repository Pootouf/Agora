<?php

namespace App\Repository\Platform;

use App\Data\SearchData;
use App\Entity\Platform\Board;
use App\Entity\Platform\Game;
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
                $query->andWhere('(size(b.listUsers) + b.nbInvitations) < b.nbUserMax');
            } elseif ($search->availability === 'CLOSE') {
                $query->andWhere('(size(b.listUsers) + b.nbInvitations) >= b.nbUserMax');
            }
        }

        if (!empty($search->datecreation)) {
            if (!empty($search->dateselection) && $search->dateselection === 'before') {
                // Option: Date avant
                $query->andWhere('b.creationDate < :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            } elseif (!empty($search->dateselection) && $search->dateselection === 'after') {
                // Option: Date après
                $query->andWhere('b.creationDate > :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            } elseif (!empty($search->dateselection) && $search->dateselection === 'between' && !empty($search->datecreationplus)) {
                // Option: Entre les dates
                $query->andWhere('b.creationDate BETWEEN :start_date AND :end_date')
                    ->setParameter('start_date', $search->datecreation)
                    ->setParameter('end_date', $search->datecreationplus);

            }else {
                $query->andWhere('b.creationDate = :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            }
        }

        if (!empty($search->game)) {
            $query->andWhere('b.game = :game')
                ->setParameter('game', $search->game);
        }

        $query->orderBy('b.creationDate', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * Results of boards linked with search
     * @return Board[]
     */
    public function searchBoardsByGame(SearchData $search, Game $game): array
    {
        $query = $this->createQueryBuilder('b');

        if (!empty($search->status)) {
            $query->andWhere('b.status = :status')
                ->setParameter('status', "{$search->status}");
        }

        if (!empty($search->availability)) {
            if ( $search->availability === 'OPEN') {
                $query->andWhere('(size(b.listUsers) + b.nbInvitations) < b.nbUserMax');
            } elseif ($search->availability === 'CLOSE') {
                $query->andWhere('(size(b.listUsers) + b.nbInvitations) >= b.nbUserMax');
            }
        }

        if (!empty($search->datecreation)) {
            if (!empty($search->dateselection) && $search->dateselection === 'before') {
                // Option: Date avant
                $query->andWhere('b.creationDate < :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            } elseif (!empty($search->dateselection) && $search->dateselection === 'after') {
                // Option: Date après
                $query->andWhere('b.creationDate > :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            } elseif (!empty($search->dateselection) && $search->dateselection === 'between' && !empty($search->datecreationplus)) {
                // Option: Entre les dates
                $query->andWhere('b.creationDate BETWEEN :start_date AND :end_date')
                    ->setParameter('start_date', $search->datecreation)
                    ->setParameter('end_date', $search->datecreationplus);

            }else {
                $query->andWhere('b.creationDate = :creationdate')
                    ->setParameter('creationdate', $search->datecreation);
            }
        }

            $query->andWhere('b.game = :game')
                ->setParameter('game', $game);

        $query->orderBy('b.creationDate', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * Results of boards that user participate
     * @return Board[]
     */
    public function findBoardsByUserAndStatus(int $userId, string $status)
    {
        return $this->createQueryBuilder('b')
            ->join('b.listUsers', 'p')
            ->where('b.status = :status')
            ->andWhere('p.id = :userId')
            ->setParameter('status', $status)
            ->setParameter('userId', $userId)
            ->orderBy('b.creationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
