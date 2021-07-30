<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TripRepository::class)
 */
class Trip
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Nom  obligatoire")
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="Date de début obligatoire")
     * @Assert\GreaterThanOrEqual(
     *     value="today",
     *     message="La date de début doit être supérieur à aujourd'hui")
     * @ORM\Column(type="datetime")
     */
    private $dateStartTime;

    /**
     * @Assert\NotBlank(message="Durée de la sortie obligatoire")
     * @ORM\Column(type="time")
     */
    private $duration;

    /**
     * @Assert\LessThanOrEqual(
     *     propertyPath="dateStartTime",
     *     message="La date limite d'inscription doit être inférieur à la date de début")
     * @ORM\Column(type="date")
     */
    private $registrationDeadline;

    /**
     * @Assert\NotBlank(message="Nombre de places obligatoire")
     * @Assert\GreaterThan(
     *     value="0",
     *     message="Doit être supérieur à 0")
     * @ORM\Column(type="integer")
     */
    private $maxRegistrations;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tripInfos;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tripsOrganized")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tripsRegistered")
     */
    private $usersRegistered;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="tripsOrganizedByCampus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campusOrganizer;

    /**
     * @ORM\ManyToOne(targetEntity=Place::class, inversedBy="trips")
     * @ORM\JoinColumn(nullable=false)
     */
    private $place;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    public function __construct()
    {
        $this->usersRegistered = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStartTime(): ?\DateTimeInterface
    {
        return $this->dateStartTime;
    }

    public function setDateStartTime(\DateTimeInterface $dateStartTime): self
    {
        $this->dateStartTime = $dateStartTime;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): self
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getMaxRegistrations(): ?int
    {
        return $this->maxRegistrations;
    }

    public function setMaxRegistrations(int $maxRegistrations): self
    {
        $this->maxRegistrations = $maxRegistrations;

        return $this;
    }

    public function getTripInfos(): ?string
    {
        return $this->tripInfos;
    }

    public function setTripInfos(?string $tripInfos): self
    {
        $this->tripInfos = $tripInfos;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersRegistered(): Collection
    {
        return $this->usersRegistered;
    }

    public function addUsersRegistered(User $usersRegistered): self
    {
        if (!$this->usersRegistered->contains($usersRegistered)) {
            $this->usersRegistered[] = $usersRegistered;
        }

        return $this;
    }

    public function removeUsersRegistered(User $usersRegistered): self
    {
        $this->usersRegistered->removeElement($usersRegistered);

        return $this;
    }

    public function getCampusOrganizer(): ?Campus
    {
        return $this->campusOrganizer;
    }

    public function setCampusOrganizer(?Campus $campusOrganizer): self
    {
        $this->campusOrganizer = $campusOrganizer;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCurrentDate(){
        return new \DateTime();
    }
}
