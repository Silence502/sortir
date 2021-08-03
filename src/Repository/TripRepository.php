<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Data\SearchTripData;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Trip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /**
     * returns research-related trips
     * @return Trip[]
     */
    public function findSearch(
        SearchTripData $searchData,
        User $user
    ): array
    {
        //dd($searchData->dateBeginning);
        $query = $this
            ->createQueryBuilder('trip');

        if (!empty($searchData->campus)) {
            $query = $query
                ->andWhere('trip.campusOrganizer = :campus')
                ->setParameter('campus', "{$searchData->campus->getId()}");
        }

        if (!empty($searchData->q)) {
            $query = $query
                ->andWhere('trip.name LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        if (!empty($searchData->dateBeginning) && !empty($searchData->dateEnding)) {
            $query = $query
                ->andWhere('trip.dateStartTime BETWEEN :dateBeginning AND :dateEnding')
                ->setParameter('dateBeginning', "{$searchData->dateBeginning->format('Y-m-d')}")
                ->setParameter('dateEnding', "{$searchData->dateEnding->format('Y-m-d')}")
            ;
        }

        if ($searchData->isOrganizer) {
            $query = $query
                ->andWhere('trip.organizer = :user')
                ->setParameter('user', "{$user->getId()}");
        }

        if ($searchData->isRegistered) {
            $query = $query
                ->andWhere(':user MEMBER OF trip.usersRegistered')
                ->setParameter('user', "{$user->getId()}");
        }

        if ($searchData->isNotRegistered) {
            $query = $query
                ->andWhere(':user NOT MEMBER OF trip.usersRegistered')
                ->setParameter('user', "{$user->getId()}");
        }

        if ($searchData->tripPassed) {
            $query = $query
                ->andWhere('trip.dateStartTime < :today ')
                ->setParameter('today', new \DateTime());
        }

        //dd($query);
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Trip[] Returns an array of Trip objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trip
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
