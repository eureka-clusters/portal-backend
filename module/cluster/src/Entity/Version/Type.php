<?php

declare(strict_types=1);

namespace Cluster\Entity\Version;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Project\Version;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_version_type")
 * @ORM\Entity
 */
class Type extends AbstractEntity
{
    public const TYPE_PO     = 'po';
    public const TYPE_FPP    = 'fpp';
    public const TYPE_LATEST = 'latest';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(unique=true) */
    private string $type = '';
    /** @ORM\Column(unique=true) */
    private string $description;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version", cascade={"persist"}, mappedBy="type")
     *
     * @var Version[]|Collections\ArrayCollection
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new Collections\ArrayCollection();
    }

    public function isLatest(): bool
    {
        return $this->type === self::TYPE_LATEST;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Type
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Type
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Type
    {
        $this->description = $description;
        return $this;
    }

    public function getVersions()
    {
        return $this->versions;
    }

    public function setVersions($versions): Type
    {
        $this->versions = $versions;
        return $this;
    }
}
