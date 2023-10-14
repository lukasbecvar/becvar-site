<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TodoRepository;

#[ORM\Entity(repositoryClass: TodoRepository::class)]
#[ORM\Table(name: 'todos')]
class Todo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $added_time = null;

    #[ORM\Column(length: 255)]
    private ?string $completed_time = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getAddedTime(): ?string
    {
        return $this->added_time;
    }

    public function setAddedTime(string $added_time): static
    {
        $this->added_time = $added_time;

        return $this;
    }

    public function getCompletedTime(): ?string
    {
        return $this->completed_time;
    }

    public function setCompletedTime(string $completed_time): static
    {
        $this->completed_time = $completed_time;

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
}
