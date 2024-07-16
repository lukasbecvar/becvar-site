<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\VisitorRepository;

/**
 * Class Visitor
 * 
 * The Visitor entity represents a visitor table in the database
 * 
 * @package App\Entity
 */
#[ORM\Table(name: 'visitors')]
#[ORM\Entity(repositoryClass: VisitorRepository::class)]
class Visitor
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_visit = null;

    #[ORM\Column(length: 255)]
    private ?string $last_visit = null;

    #[ORM\Column(length: 255)]
    private ?string $browser = null;

    #[ORM\Column(length: 255)]
    private ?string $os = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    #[ORM\Column(length: 255)]
    private ?string $banned_status = null;

    #[ORM\Column(length: 255)]
    private ?string $ban_reason = null;

    #[ORM\Column(length: 255)]
    private ?string $banned_time = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstVisit(): ?string
    {
        return $this->first_visit;
    }

    public function setFirstVisit(string $first_visit): static
    {
        $this->first_visit = $first_visit;

        return $this;
    }

    public function getLastVisit(): ?string
    {
        return $this->last_visit;
    }

    public function setLastVisit(string $last_visit): static
    {
        $this->last_visit = $last_visit;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): static
    {
        $this->ip_address = $ip_address;

        return $this;
    }

    public function getBannedStatus(): ?string
    {
        return $this->banned_status;
    }

    public function setBannedStatus(string $banned_status): static
    {
        $this->banned_status = $banned_status;

        return $this;
    }

    public function getBanReason(): ?string
    {
        return $this->ban_reason;
    }

    public function setBanReason(string $ban_reason): static
    {
        $this->ban_reason = $ban_reason;

        return $this;
    }

    public function getBannedTime(): ?string
    {
        return $this->banned_time;
    }

    public function setBannedTime(string $banned_time): static
    {
        $this->banned_time = $banned_time;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
