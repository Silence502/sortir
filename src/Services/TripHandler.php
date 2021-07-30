<?php

namespace App\Services;

use App\Entity\Trip;

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
            } elseif ($intervalStartDate == 1) {
                $stateId = 5;
            }

//            dd($interval = $today->diff($trip->getDateStartTime()));
//            dd($interval = $trip->getDateStartTime()->diff($today));
//            dd($intervalRegistrationDeadline);
//            dd($stateId);

            // stateId = Créée
        } else {
            $stateId = 1;
        }

        return $stateId;

    }
}