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
     * @ORM\ManyToOne(targetEntity=Trucker::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trucker;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $from_address;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class)
     */
    private $to_address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $check_in;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $check_out;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckIn(): ?string
    {
        return $this->check_in;
    }

    public function setCheckIn(?string $check_in): self
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

    public function getTrucker(): ?Trucker
    {
        return $this->trucker;
    }

    public function setTrucker(?Trucker $trucker): self
    {
        $this->trucker = $trucker;

        return $this;
    }

    public function getFromAddress(): ?Address
    {
        return $this->from_address;
    }

    public function setFromAddress(?Address $from_address): self
    {
        $this->from_address = $from_address;

        return $this;
    }

    public function getToAddress(): ?Address
    {
        return $this->to_address;
    }

    public function setToAddress(?Address $to_address): self
    {
        $this->to_address = $to_address;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'trucker_id' => $this->getTrucker()->getId(),
            'fromAddress' => $this->getFromAddress(),
            'toAddress' => $this->getToAddress(),
            'check_in' => $this->getCheckIn(),
            'check_out' => $this->getCheckOut(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/tracking/' . $this->getId()
                ],
                [
                    'rel' => 'trucker',
                    'path' => '/truckers/' . $this->getTrucker()->getId()
                ]
            ]
        ];
    }
}
