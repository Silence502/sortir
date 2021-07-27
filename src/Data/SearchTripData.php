<?php


namespace App\Data;


use App\Entity\Campus;

class SearchTripData
{

    public ?Campus $campus = null;

    public ?string $q = '';

    public ?\DateTime $dateBeginning = null;

    public ?\DateTime $dateEnding = null;

    public ?bool $isOrganizer = null;

    public ?bool $isRegistered = null;

    public ?bool $isNotRegistered = null;

    public ?bool $tripPassed = null;
}
