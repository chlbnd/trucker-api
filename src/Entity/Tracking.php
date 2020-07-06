<?php

namespace App\Entity;

use App\Repository\TrackingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrackingRepository::class)
 */
class Tracking implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Trucker::class, inversedBy="trackings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trucker;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $from_lat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $from_lon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $to_lat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $to_lon;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $check_in;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $check_out;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrucker(): ?Trucker
    {
        return $this->trucker;
    }

    public function setTrucker(?Trucker $trucker): self
    {
        $this->trucker = $trucker;

        return $this;
    }

    public function getFromLat(): ?string
    {
        return $this->from_lat;
    }

    public function setFromLat(string $from_lat): self
    {
        $this->from_lat = $from_lat;

        return $this;
    }

    public function getFromLon(): ?string
    {
        return $this->from_lon;
    }

    public function setFromLon(string $from_lon): self
    {
        $this->from_lon = $from_lon;

        return $this;
    }

    public function getToLat(): ?string
    {
        return $this->to_lat;
    }

    public function setToLat(string $to_lat): self
    {
        $this->to_lat = $to_lat;

        return $this;
    }

    public function getToLon(): ?string
    {
        return $this->to_lon;
    }

    public function setToLon(string $to_lon): self
    {
        $this->to_lon = $to_lon;

        return $this;
    }

    public function getCheckIn(): ?string
    {
        return $this->check_in;
    }

    public function setCheckIn(string $check_in): self
    {
        $this->check_in = $check_in;

        return $this;
    }

    public function getCheckOut(): ?string
    {
        return $this->check_out;
    }

    public function setCheckOut(?string $check_out): self
    {
        $this->check_out = $check_out;

        return $this;
    }

    public function jsonSerialize()
    {
        return [];
    }
}
