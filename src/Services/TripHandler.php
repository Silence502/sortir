<?php

namespace App\Services;

use App\Entity\Trip;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;

class TripHandler
{
    public function publish(Trip $trip): Trip
    {
        $trip->setIsPublished(true);

        return $trip;
    }

    public function setTripState(
        Trip $trip
    ): int
    {
        $stateId = 0;

        $today = new \DateTime();
        $intervalStartDate = $today->diff($trip->getDateStartTime());
        $intervalRegistrationDeadline = $today->diff($trip->getRegistrationDeadline());

        if ($trip->getIsPublished()) {
            // If trip is cancelled, dont change and return
            if ($trip->getState()->getId() == 6) {
                $stateId = 6;
                return $stateId;
            }

            // stateId = Ouverte
            if ($intervalStartDate->invert == 0
                && $intervalRegistrationDeadline->invert == 0) {
                $stateId = 2;
                // stateId = Cloturée
            } elseif ($intervalStartDate->invert == 0
                && $intervalRegistrationDeadline->invert == 1) {
                $stateId = 3;
                //stateId = En cours
                //TODO state En cours
            } elseif ($trip->getDateStartTime() == $today) {
                $stateId = 4;
                // stateId = Passée
            } elseif ($intervalStartDate->invert == 1) {
                $stateId = 5;
            }

            // stateId = Créée
        } else {
            $stateId = 1;
        }

        return $stateId;

    }

    public function setStateAndFlush(
        Trip $trip,
        EntityManagerInterface $entityManager,
        StateRepository $stateRepository
    )
    {
        $trip->setState(
            $stateRepository->find($this->setTripState($trip))
        );

        $entityManager->persist($trip);
        $entityManager->flush();

    }
}