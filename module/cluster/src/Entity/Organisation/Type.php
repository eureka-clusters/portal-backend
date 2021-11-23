<?php

declare(strict_types=1);

namespace Cluster\Entity\Organisation;

use Application\Entity\AbstractEntity;
use Cluster\Entity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_organisation_type")
 * @ORM\Entity
 */
class Type extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /** @ORM\Column(unique=true) */
    private string $type;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Organisation", cascade={"persist"}, mappedBy="type")
     *
     * @var Entity\Organisation[]|Collections\ArrayCollection
     */
    private $organisations;

    public function __construct()
    {
        $this->organisations = new Collections\ArrayCollection();
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

    public function getOrganisations()
    {
        return $this->organisations;
    }

    public function setOrganisations($organisations): Type
    {
        $this->organisations = $organisations;
        return $this;
    }
}
