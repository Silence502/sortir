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
        //dd($user);
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

        if ($searchData->isOrganizer) {
            $query = $query
                ->andWhere('trip.organizer = :user')
                ->setParameter('user', "{$user->getId()}");
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
