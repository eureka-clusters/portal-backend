<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity\Version;

use Application\Entity\AbstractEntity;
use Cluster\Entity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_version_status")
 * @ORM\Entity
 */
class Status extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(unique=true)
     */
    private string $status;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Version", cascade={"persist"}, mappedBy="status")
     *
     * @var \Cluster\Entity\Project\Version[]|Collections\ArrayCollection
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Status
    {
        $this->id = $id;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Status
    {
        $this->status = $status;
        return $this;
    }

    public function getVersions()
    {
        return $this->versions;
    }

    public function setVersions($versions): Status
    {
        $this->versions = $versions;
        return $this;
    }
}
