<?php

declare(strict_types=1);

namespace Cluster\Entity\Project;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project;
use Cluster\Repository\Project\AreaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'cluster_project_area')]
#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area extends AbstractEntity
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'], inversedBy: 'areas')]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\Column(nullable: false)]
    private string $code = '';

    #[ORM\Column(nullable: false)]
    private string $label = '';

    #[ORM\Column(nullable: false)]
    private string $type = '';

    public function __construct()
    {
        $this->project = new Project();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Area
    {
        $this->id = $id;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): Area
    {
        $this->project = $project;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Area
    {
        $this->code = $code;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): Area
    {
        $this->label = $label;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Area
    {
        $this->type = $type;
        return $this;
    }
}