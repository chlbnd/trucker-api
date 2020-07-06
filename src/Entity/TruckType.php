<?php

namespace App\Entity;

use App\Repository\TruckTypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TruckTypeRepository::class)
 */
class TruckType implements EntityInterface
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
     * @ORM\OneToMany(targetEntity=Trucker::class, mappedBy="truck_type", orphanRemoval=true)
     */
    private $truckers;

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

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/truck_types/' . $this->getId()
                ]
            ]
        ];
    }
}
