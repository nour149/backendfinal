<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(nullable: true)]
    private ?bool $archived = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    /**
     * @ORM\OneToMany(targetEntity="AccessProject", mappedBy="projet")
     */
    private $accessProjects;

    /**
     * @ORM\ManyToOne(targetEntity="Tier", inversedBy="projets")
     * @ORM\JoinColumn(name="tier_id", referencedColumnName="id")
     */
    private $tier;

    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="projet")
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="projet")
     */
    private $tasks;

    public function __construct()
    {
        $this->accessProjects = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getAccessProjects(): Collection
    {
        return $this->accessProjects ?? new ArrayCollection();
    }

    public function setTier(?Tier $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function getSections(): Collection
    {
        return $this->sections?? new ArrayCollection();
    }

    public function getTasks(): Collection
    {
        return $this->tasks?? new ArrayCollection();
    }
}
