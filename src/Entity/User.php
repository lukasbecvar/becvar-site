<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    private ?string $ip_address = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(length: 255)]
    private ?string $registed_time = null;

    #[ORM\Column(length: 255)]
    private ?string $last_login_time = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $profile_pic = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $status_update_time = null;

    #[ORM\Column]
    private ?string $visitor_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getRegistedTime(): ?string
    {
        return $this->registed_time;
    }

    public function setRegistedTime(string $registed_time): static
    {
        $this->registed_time = $registed_time;

        return $this;
    }

    public function getLastLoginTime(): ?string
    {
        return $this->last_login_time;
    }

    public function setLastLoginTime(string $last_login_time): static
    {
        $this->last_login_time = $last_login_time;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profile_pic;
    }

    public function setProfilePic(string $profile_pic): static
    {
        $this->profile_pic = $profile_pic;

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

    public function getStatusUpdateTime(): ?string
    {
        return $this->status_update_time;
    }

    public function setStatusUpdateTime(string $status_update_time): static
    {
        $this->status_update_time = $status_update_time;

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
