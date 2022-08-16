<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Organisation;
use Cluster\Entity\Project;
use Cluster\Entity\Project\Partner\Funding;
use Cluster\Entity\Project\Version\CostsAndEffort;
use Cluster\Repository\Project\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'cluster_project_partner')]
#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Organisation::class, cascade: ['persist'], inversedBy: 'partners')]
    #[ORM\JoinColumn(nullable: false)]
    private Organisation $organisation;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'partners')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\Column(unique: true)]
    #[Gedmo\Slug(fields: ['projectName', 'organisationName'], updatable: true)]
    private string $slug;

    #[ORM\Column]
    private string $organisationName;

    #[ORM\Column]
    private string $projectName;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    #[ORM\Column(type: 'boolean')]
    private bool $isCoordinator;

    #[ORM\Column(type: 'boolean')]
    private bool $isSelfFunded;

    #[ORM\Column(type: 'array')]
    private array $technicalContact = [];

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: CostsAndEffort::class, cascade: ['persist'])]
    private Collection $costsAndEffort;

    #[ORM\Column(type: 'float')]
    private float $latestVersionCosts;

    #[ORM\Column(type: 'float')]
    private float $latestVersionEffort;

    #[ORM\OneToMany(mappedBy: 'partner', targetEntity: Funding::class, cascade: ['persist'])]
    private Collection $funding;

    public function __construct()
    {
        $this->organisation = new Organisation();
        $this->project = new Project();
        $this->costsAndEffort = new ArrayCollection();
        $this->funding = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Partner
    {
        $this->id = $id;
        return $this;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(Organisation $organisation): Partner
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Partner
    {
        $this->project = $project;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Partner
    {
        $this->slug = $slug;
        return $this;
    }

    public function getOrganisationName(): string
    {
        return $this->organisationName;
    }

    public function setOrganisationName(string $organisationName): Partner
    {
        $this->organisationName = $organisationName;
        return $this;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): Partner
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): Partner
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isCoordinator(): bool
    {
        return $this->isCoordinator;
    }

    public function setIsCoordinator(bool $isCoordinator): Partner
    {
        $this->isCoordinator = $isCoordinator;
        return $this;
    }

    public function isSelfFunded(): bool
    {
        return $this->isSelfFunded;
    }

    public function setIsSelfFunded(bool $isSelfFunded): Partner
    {
        $this->isSelfFunded = $isSelfFunded;
        return $this;
    }

    public function getTechnicalContact(): array
    {
        return $this->technicalContact;
    }

    public function setTechnicalContact(array $technicalContact): Partner
    {
        $this->technicalContact = $technicalContact;
        return $this;
    }

    public function getCostsAndEffort(): ArrayCollection|Collection
    {
        return $this->costsAndEffort;
    }

    public function setCostsAndEffort(ArrayCollection|Collection $costsAndEffort): Partner
    {
        $this->costsAndEffort = $costsAndEffort;
        return $this;
    }

    public function getLatestVersionCosts(): float
    {
        return $this->latestVersionCosts;
    }

    public function setLatestVersionCosts(float $latestVersionCosts): Partner
    {
        $this->latestVersionCosts = $latestVersionCosts;
        return $this;
    }

    public function getLatestVersionEffort(): float
    {
        return $this->latestVersionEffort;
    }

    public function setLatestVersionEffort(float $latestVersionEffort): Partner
    {
        $this->latestVersionEffort = $latestVersionEffort;
        return $this;
    }

    public function getFunding(): Collection
    {
        return $this->funding;
    }

    public function setFunding(Collection $funding): Partner
    {
        $this->funding = $funding;
        return $this;
    }
}
