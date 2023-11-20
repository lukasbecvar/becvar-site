<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TodoRepository;

#[ORM\Table(name: 'todos')]
#[ORM\Entity(repositoryClass: TodoRepository::class)]
class Todo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;
    
    #[ORM\Column(length: 255)]
    private ?string $added_time = null;

    #[ORM\Column(length: 255)]
    private ?string $completed_time = null;

    #[ORM\Column(length: 255)]
    private ?string $added_by = null;

    #[ORM\Column(length: 2555)]
    private ?string $closed_by = null;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

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

    public function getAddedBy(): ?string
    {
        return $this->added_by;
    }

    public function setAddedBy(string $added_by): static
    {
        $this->added_by = $added_by;

        return $this;
    }

    public function getClosedBy(): ?string
    {
        return $this->closed_by;
    }

    public function setClosedBy(string $closed_by): static
    {
        $this->closed_by = $closed_by;

        return $this;
    }
}
