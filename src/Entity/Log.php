<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LogRepository;

#[ORM\Table(name: 'logs')]
#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    private ?string $time = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    #[ORM\Column(length: 255)]
    private ?string $browser = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    #[ORM\JoinColumn(name: "visitors", referencedColumnName: "id")]
    private ?string $visitor_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): static
    {
        $this->time = $time;

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

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): static
    {
        $this->browser = $browser;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVisitorId(): ?string
    {
        return $this->visitor_id;
    }

    public function setVisitorId(string $visitor_id): static
    {
        $this->visitor_id = $visitor_id;

        return $this;
    }
}
