<?php

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project\Evaluation;
use Cluster\Entity\Project\Partner;
use Cluster\Entity\Project\Status;
use Cluster\Entity\Project\Version;
use Cluster\Repository\ProjectRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'cluster_project')]
#[ORM\Index(columns: ['identifier'], name: 'identifier_index')]
#[ORM\Index(columns: ['number', 'name', 'title', 'description'], flags: ['FULLTEXT'])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $identifier = '';

    #[ORM\Column(unique: true)]
    #[Gedmo\Slug(fields: ['name'], updatable: true)]
    private string $slug;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?DateTime $dateUpdated = null;

    #[ORM\Column]
    private string $number = '';

    #[ORM\Column]
    private string $name = '';

    #[ORM\Column]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?string $technicalArea = null;

    #[ORM\Column]
    private string $programme = '';

    #[ORM\Column]
    private string $programmeCall = '';

    #[ORM\ManyToOne(targetEntity: Cluster::class, cascade: ['persist'], inversedBy: 'projectsPrimary')]
    #[ORM\JoinColumn(nullable: false)]
    private Cluster $primaryCluster;

    #[ORM\ManyToOne(targetEntity: Cluster::class, cascade: ['persist'], inversedBy: 'projectsSecondary')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Cluster $secondaryCluster = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $labelDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $cancelDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $officialStartDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $officialEndDate = null;

    #[ORM\ManyToOne(targetEntity: Status::class, cascade: ['persist'], inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[ORM\Column(type: 'array')]
    private array $projectLeader = [];

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Version::class, cascade: ['persist', 'remove'])]
    private Collection $versions;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Partner::class, cascade: ['persist', 'remove'])]
    private Collection $partners;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Evaluation::class, cascade: ['persist', 'remove'])]
    private Collection $evaluation;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $projectOutlineCosts = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $projectOutlineEffort = null;

    #[ORM\Column(type: 'float')]
    private float $fullProjectProposalCosts = 0;

    #[ORM\Column(type: 'float')]
    private float $fullProjectProposalEffort = 0;

    #[ORM\Column(type: 'float')]
    private float $latestVersionCosts = 0;

    #[ORM\Column(type: 'float')]
    private float $latestVersionEffort = 0;

    public function __construct()
    {
        $this->dateCreated    = new DateTime();
        $this->primaryCluster = new Cluster();
        $this->status         = new Status();
        $this->versions       = new ArrayCollection();
        $this->partners       = new ArrayCollection();
        $this->evaluation     = new ArrayCollection();
    }

    public function parseCacheKey(): string
    {
        return sprintf('project-%s-%d', $this->getResourceId(), null === $this->dateUpdated ? $this->dateCreated->getTimestamp() : $this->dateUpdated->getTimestamp());
    }

    public function __toString(): string
    {
        return (string)$this->title;
    }

    public function hasSecondaryCluster(): bool
    {
        return null !== $this->secondaryCluster;
    }

    public function getLatestVersion(): ?Version
    {
        return $this->versions->filter(p: fn(Version $version) => $version->getType()->isLatest())->first() ?: null;
    }

    public function getCoordinatorPartner(): ?Partner
    {
        return $this->partners->filter(p: fn(Partner $partner) => $partner->isCoordinator())->first() ?: null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Project
    {
        $this->id = $id;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): Project
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Project
    {
        $this->slug = $slug;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): Project
    {
        $this->number = $number;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Project
    {
        $this->name = $name;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Project
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Project
    {
        $this->description = $description;
        return $this;
    }

    public function getTechnicalArea(): ?string
    {
        return $this->technicalArea;
    }

    public function setTechnicalArea(?string $technicalArea): Project
    {
        $this->technicalArea = $technicalArea;
        return $this;
    }

    public function getProgramme(): string
    {
        return $this->programme;
    }

    public function setProgramme(string $programme): Project
    {
        $this->programme = $programme;
        return $this;
    }

    public function getProgrammeCall(): string
    {
        return $this->programmeCall;
    }

    public function setProgrammeCall(string $programmeCall): Project
    {
        $this->programmeCall = $programmeCall;
        return $this;
    }

    public function getPrimaryCluster(): Cluster
    {
        return $this->primaryCluster;
    }

    public function setPrimaryCluster(Cluster $primaryCluster): Project
    {
        $this->primaryCluster = $primaryCluster;
        return $this;
    }

    public function getSecondaryCluster(): ?Cluster
    {
        return $this->secondaryCluster;
    }

    public function setSecondaryCluster(?Cluster $secondaryCluster): Project
    {
        $this->secondaryCluster = $secondaryCluster;
        return $this;
    }

    public function getLabelDate(): ?DateTime
    {
        return $this->labelDate;
    }

    public function setLabelDate(?DateTime $labelDate): Project
    {
        $this->labelDate = $labelDate;
        return $this;
    }

    public function getCancelDate(): ?DateTime
    {
        return $this->cancelDate;
    }

    public function setCancelDate(?DateTime $cancelDate): Project
    {
        $this->cancelDate = $cancelDate;
        return $this;
    }

    public function getOfficialStartDate(): ?DateTime
    {
        return $this->officialStartDate;
    }

    public function setOfficialStartDate(?DateTime $officialStartDate): Project
    {
        $this->officialStartDate = $officialStartDate;
        return $this;
    }

    public function getOfficialEndDate(): ?DateTime
    {
        return $this->officialEndDate;
    }

    public function setOfficialEndDate(?DateTime $officialEndDate): Project
    {
        $this->officialEndDate = $officialEndDate;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): Project
    {
        $this->status = $status;
        return $this;
    }

    public function getProjectLeader(): array
    {
        return $this->projectLeader;
    }

    public function setProjectLeader(array $projectLeader): Project
    {
        $this->projectLeader = $projectLeader;
        return $this;
    }

    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function setVersions(Collection $versions): Project
    {
        $this->versions = $versions;
        return $this;
    }

    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function setPartners(Collection $partners): Project
    {
        $this->partners = $partners;
        return $this;
    }

    public function getEvaluation(): Collection
    {
        return $this->evaluation;
    }

    public function setEvaluation(Collection $evaluation): Project
    {
        $this->evaluation = $evaluation;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Project
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): Project
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getProjectOutlineCosts(): ?float
    {
        return $this->projectOutlineCosts;
    }

    public function setProjectOutlineCosts(?float $projectOutlineCosts): Project
    {
        $this->projectOutlineCosts = $projectOutlineCosts;
        return $this;
    }

    public function getProjectOutlineEffort(): ?float
    {
        return $this->projectOutlineEffort;
    }

    public function setProjectOutlineEffort(?float $projectOutlineEffort): Project
    {
        $this->projectOutlineEffort = $projectOutlineEffort;
        return $this;
    }

    public function getFullProjectProposalCosts(): float
    {
        return $this->fullProjectProposalCosts;
    }

    public function setFullProjectProposalCosts(float $fullProjectProposalCosts): Project
    {
        $this->fullProjectProposalCosts = $fullProjectProposalCosts;
        return $this;
    }

    public function getFullProjectProposalEffort(): float
    {
        return $this->fullProjectProposalEffort;
    }

    public function setFullProjectProposalEffort(float $fullProjectProposalEffort): Project
    {
        $this->fullProjectProposalEffort = $fullProjectProposalEffort;
        return $this;
    }

    public function getLatestVersionCosts(): float
    {
        return $this->latestVersionCosts;
    }

    public function setLatestVersionCosts(float $latestVersionCosts): Project
    {
        $this->latestVersionCosts = $latestVersionCosts;
        return $this;
    }

    public function getLatestVersionEffort(): float
    {
        return $this->latestVersionEffort;
    }

    public function setLatestVersionEffort(float $latestVersionEffort): Project
    {
        $this->latestVersionEffort = $latestVersionEffort;
        return $this;
    }
}
