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
        $dateFormat = 'd-m-Y';
        $today = new \DateTime();


        if ($trip->getIsPublished()) {

            // stateId = Ouverte
            if ($trip->getDateStartTime() < $today
                && $trip->getRegistrationDeadline() < $today) {
                $stateId = 2;
                // stateId = Cloturée
            } elseif ($trip->getDateStartTime() < $today
                && $trip->getRegistrationDeadline() > $today) {
                $stateId = 3;
                //stateId = En cours
            } elseif ($trip->getDateStartTime() == $today) {
                $stateId = 4;
                // stateId = Passée
            } elseif ($trip->getDateStartTime() > $today) {
                $stateId = 5;
            }

            dd($trip->getDateStartTime()->format('d-m-Y') == $today->format('d-m-Y'));
            
            // stateId = Créée
        } else {
            $stateId = 1;
        }

        return $stateId;

    }
}