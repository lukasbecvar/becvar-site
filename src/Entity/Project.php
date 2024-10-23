<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjectRepository;

/**
 * Class Project
 *
 * The Project entity represents a project table in the database
 *
 * @package App\Entity
 */
#[ORM\Table(name: 'projects')]
#[ORM\Index(name: 'projects_name_idx', columns: ['name'])]
#[ORM\Index(name: 'projects_status_idx', columns: ['status'])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $technology = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $link = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * Get the project id
     *
     * @return int|null The project id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the project name
     *
     * @return string|null The project name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the project name
     *
     * @param string $name The project name
     *
     * @return static The project object
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the project description
     *
     * @return string|null The project description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the project description
     *
     * @param string $description The project description
     *
     * @return static The project object
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the project technology
     *
     * @return string|null The project technology
     */
    public function getTechnology(): ?string
    {
        return $this->technology;
    }

    /**
     * Set the project technology
     *
     * @param string $technology The project technology
     *
     * @return static The project object
     */
    public function setTechnology(string $technology): static
    {
        $this->technology = $technology;

        return $this;
    }

    /**
     * Get the project link
     *
     * @return string|null The project link
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * Set the project link
     *
     * @param string $link The project link
     *
     * @return static The project object
     */
    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get the project status
     *
     * @return string|null The project status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the project status
     *
     * @param string $status The project status
     *
     * @return static The project object
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
