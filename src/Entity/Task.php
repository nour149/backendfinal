<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endf = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $allTheDay = null;

    #[ORM\Column(length: 255)]
    private ?string $priority = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEndf(): ?\DateTimeInterface
    {
        return $this->endf;
    }

    public function setEndf(\DateTimeInterface $endf): static
    {
        $this->endf = $endf;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getAllTheDay(): ?string
    {
        return $this->allTheDay;
    }

    public function setAllTheDay(string $allTheDay): static
    {
        $this->allTheDay = $allTheDay;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }
    public function getproject(): ?string
    {
        return $this->project;
    }

    public function getuser(): ?string
    {
        return $this->user;
    }
    public function getsection(): ?string
    {
        return $this->section;
    }
    public function getTier(): ?string
    {
        return $this->tier;
    }











    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="subtasks")
     * @ORM\JoinColumn(name="parent_task_id", referencedColumnName="id")
     */
    private $parentTask;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="tasks")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity="Tier", inversedBy="tasks")
     * @ORM\JoinColumn(name="tier_id", referencedColumnName="id")
     */
    private $tier;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="parentTask")
     */
    private $subtasks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;
    public function __construct()
    {
        $this->subtasks = new ArrayCollection();

    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
