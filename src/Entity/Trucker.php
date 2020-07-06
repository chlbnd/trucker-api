<?php

namespace App\Entity;

use App\Repository\TruckerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TruckerRepository::class)
 */
class Trucker implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_owner;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $cnh_type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_loaded;

    /**
     * @ORM\ManyToOne(targetEntity=TruckType::class, inversedBy="truckers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $truck_type;

    /**
     * @ORM\OneToMany(targetEntity=Tracking::class, mappedBy="trucker", orphanRemoval=true)
     */
    private $trackings;

    public function __construct()
    {
        $this->trackings = new ArrayCollection();
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

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getIsOwner(): ?bool
    {
        return $this->is_owner;
    }

    public function setIsOwner(bool $is_owner): self
    {
        $this->is_owner = $is_owner;

        return $this;
    }

    public function getCnhType(): ?string
    {
        return $this->cnh_type;
    }

    public function setCnhType(string $cnh_type): self
    {
        $this->cnh_type = $cnh_type;

        return $this;
    }

    public function getIsLoaded(): ?bool
    {
        return $this->is_loaded;
    }

    public function setIsLoaded(bool $is_loaded): self
    {
        $this->is_loaded = $is_loaded;

        return $this;
    }

    public function getTruckType(): ?TruckType
    {
        return $this->truck_type;
    }

    public function setTruckType(?TruckType $truck_type): self
    {
        $this->truck_type = $truck_type;

        return $this;
    }

    /**
     * @return Collection|Tracking[]
     */
    public function getTrackings(): Collection
    {
        return $this->trackings;
    }

    public function addTracking(Tracking $tracking): self
    {
        if (!$this->trackings->contains($tracking)) {
            $this->trackings[] = $tracking;
            $tracking->setTrucker($this);
        }

        return $this;
    }

    public function removeTracking(Tracking $tracking): self
    {
        if ($this->trackings->contains($tracking)) {
            $this->trackings->removeElement($tracking);

            if ($tracking->getTrucker() === $this) {
                $tracking->setTrucker(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'birthdate' => $this->getBirthdate(),
            'gender' => $this->getGender(),
            'is_owner' => $this->getIsOwner(),
            'cnh_type' => $this->getCnhType(),
            'is_loaded' => $this->getIsLoaded(),
            'truck_type' => $this->getTruckType(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/truckers/' . $this->getId()
                ],
                [
                    'rel' => 'truck_type',
                    'path' => '/truck_types/' . $this->getTruckType()->getId()
                ]
            ]
        ];
    }
}
