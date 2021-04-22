<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_funder")
 * @ORM\Entity(repositoryClass="Program\Repository\Funder")
 */
class Funder extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\OneToOne(targetEntity="Admin\Entity\User",  cascade={"persist"}, inversedBy="funder")
     * @ORM\JoinColumn(nullable=false)
     */
    private \Admin\Entity\User $user;
    /**
     * @ORM\ManyToOne(targetEntity="Cluster\Entity\Country", inversedBy="funder", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Country $country;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Funder
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): \Admin\Entity\User
    {
        return $this->user;
    }

    public function setUser(\Admin\Entity\User $user): Funder
    {
        $this->user = $user;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): Funder
    {
        $this->country = $country;
        return $this;
    }
}
