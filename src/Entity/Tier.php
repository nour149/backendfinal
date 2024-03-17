<?php

namespace App\Entity;

use App\Entity\Projet;
use App\Entity\Task;
use App\Repository\TierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Project;
use DateTime;

#[ORM\Entity(repositoryClass: TierRepository::class)]
class Tier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $tel = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $town = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vat = null;

    #[ORM\Column(length: 255)]
    private ?string $rne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstResp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailfirstResp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telfirstResp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $archived = null;

    #[ORM\Column(nullable: true)]
    private ?bool $blocked = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255)]
    private ?string $relation = null;

    /**
     * @ORM\OneToMany(targetEntity="Projet", mappedBy="accessTier")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="accessTier")
     */
    private $tasks;

    public function __construct()
    {    $this->name = 'Default Name';
        $this->tel= 'Default Tel';
        $this->rne = 'Default Rne';
        $this->relation = 'Default relation';
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function getTelfirstResp(): ?string
    {
        return $this->telfirstResp;
    }
    public function getAdress(): ?string
    {
        return $this->adress;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getTown(): ?string
    {
        return $this->town;
    }
    public function getCountry(): ?string
    {
        return $this->country;
    }
    public function getVat(): ?string
    {
        return $this->vat;
    }
    public function getRne(): ?string
    {
        return $this->rne;
    }
    public function getFirstResp(): ?string
    {
        return $this->firstResp;
    }
    public function getEmailfirstResp(): ?string
    {
        return $this->emailfirstResp;
    }
    public function getLogo(): ?string
    {
        return $this->logo;
    }
    public function isArchived(): ?string
    {
        return $this->archived;
    }
    public function isBlocked(): ?string
    {
        return $this->blocked;
    }
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }
    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    // ... getter and setter methods for other properties ...

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects ?? new ArrayCollection();
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks ?? new ArrayCollection();
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function setTel(?string $tel): void
    {
        $this->tel = $tel;
    }
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
    public function setRne(?string $rne): void
    {
        $this->rne = $rne;
    }

    public function setProjects(ArrayCollection $updatedProjects): void
    {
        $this->projects = $updatedProjects;
    }

    public function setcreated_at(DateTime $created_at): void
    {
        $this->created_at =$created_at;
    }
    public function setupdated_at(DateTime $setupdated_at): void
    {
        $this->updated_at =$setupdated_at;
    }
    public function setRelation(?string $relation): void
    {
        $this->relation =$relation;
    }

}
