<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Country;
use Cluster\Entity\Funding\Status;
use Cluster\Entity\Project;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Table(name: 'cluster_project_evaluation')]
#[ORM\Entity]
class Evaluation extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private string $description = '';

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $dateCreated;

    #[ORM\ManyToOne(targetEntity: Status::class, cascade: ['persist'], inversedBy: 'evaluation')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'evaluation')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Country::class, cascade: ['persist'], inversedBy: 'evaluation')]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'evaluation')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    /**
     * If this column is filled we know for which version this evaluation is (PO or FPP)
     * WHen the column is null then it represents the overall funding status of this country (global PA evaluation)*/
    #[ORM\OneToOne(inversedBy: 'evaluation', targetEntity: Version::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Version $projectVersion = null;

    public function __construct()
    {
        $this->dateCreated = new DateTime();

        $this->status  = new Status();
        $this->user    = new User();
        $this->country = new Country();
        $this->project = new Project();
    }

    public function isFundingStatus(): bool
    {
        return null === $this->projectVersion;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Evaluation
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Evaluation
    {
        $this->description = $description;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): Evaluation
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): Evaluation
    {
        $this->status = $status;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Evaluation
    {
        $this->user = $user;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Evaluation
    {
        $this->country = $country;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Evaluation
    {
        $this->project = $project;
        return $this;
    }

    public function getProjectVersion(): ?Version
    {
        return $this->projectVersion;
    }

    public function setProjectVersion(?Version $projectVersion): Evaluation
    {
        $this->projectVersion = $projectVersion;
        return $this;
    }
}
