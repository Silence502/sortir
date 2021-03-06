<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Trip::class, mappedBy="campusOrganizer")
     */
    private $tripsOrganizedByCampus;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="campus")
     */
    private $users;

    public function __construct()
    {
        $this->tripsOrganizedByCampus = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection|Trip[]
     */
    public function getTripsOrganizedByCampus(): Collection
    {
        return $this->tripsOrganizedByCampus;
    }

    public function addTripsOrganizedByCampus(Trip $tripsOrganizedByCampus): self
    {
        if (!$this->tripsOrganizedByCampus->contains($tripsOrganizedByCampus)) {
            $this->tripsOrganizedByCampus[] = $tripsOrganizedByCampus;
            $tripsOrganizedByCampus->setCampusOrganizer($this);
        }

        return $this;
    }

    public function removeTripsOrganizedByCampus(Trip $tripsOrganizedByCampus): self
    {
        if ($this->tripsOrganizedByCampus->removeElement($tripsOrganizedByCampus)) {
            // set the owning side to null (unless already changed)
            if ($tripsOrganizedByCampus->getCampusOrganizer() === $this) {
                $tripsOrganizedByCampus->setCampusOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCampus($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCampus() === $this) {
                $user->setCampus(null);
            }
        }

        return $this;
    }
}
