<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Cluster\Entity\Statistics;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cluster_statistics_partner",
 *     indexes={
 *      @ORM\Index(name="identifier_index", columns={"identifier"}),
 *      @ORM\Index(name="partner_identifier_index", columns={"partnerIdentifier"})
 * })
 * @ORM\Entity(repositoryClass="Cluster\Repository\Statistics\PartnerRepository")
 */
class Partner
{
    public const RESULT_PROJECT = 1;
    public const RESULT_PARTNER = 2;
    public const RESULT_CHART   = 3;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\Column()
     */
    private string $identifier;
    /**
     * @ORM\Column()
     */
    private string $projectNumber;
    /**
     * @ORM\Column()
     */
    private string $projectName;
    /**
     * @ORM\Column()
     */
    private string $projectTitle;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $projectDescription;
    /**
     * @ORM\Column(nullable=true)
     */
    private string $technicalArea;
    /**
     * @ORM\Column()
     */
    private string $programme;
    /**
     * @ORM\Column()
     */
    private string $programmeCall;
    /**
     * @ORM\Column()
     */
    private string $primaryCluster;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $secondaryCluster = null;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $labelDate = null;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $cancelDate = null;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $officialStartDate = null;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $officialEndDate = null;
    /**
     * @ORM\Column()
     */
    private string $projectStatus;
    /**
     * @ORM\Column(type="array")
     */
    private array $projectLeader;


    /**
     * @ORM\Column()
     */
    private string $partner;
    /**
     * @ORM\Column()
     */
    private string $partnerIdentifier;
    /**
     * @ORM\Column()
     */
    private string $country;
    /**
     * @ORM\Column()
     */
    private string $partnerType;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $coordinator;
    /**
     * @ORM\Column(type="boolean")
     */
    private bool $selfFunded;
    /**
     * @ORM\Column(type="array")
     */
    private array $technicalContact;
    /**
     * @ORM\Column(type="integer")
     */
    private int $year;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $poSubmissionDate = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $poStatus;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poTotalEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poTotalCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poEffortInYear = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $poCostsInYear = null;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private array $poCountries = [];


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $fppSubmissionDate = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $fppStatus = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppTotalEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppTotalCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppEffortInYear = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $fppCostsInYear = null;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $fppCountries = null;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $latestVersionSubmissionDate = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $latestVersionStatus = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $latestVersionType = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionTotalEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionTotalCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionEffort = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionCosts = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionEffortInYear = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latestVersionCostsInYear = null;
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $latestVersionCountries = null;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value): void
    {
        $this->$name = $value;
    }

    public function __isset($name): bool
    {
        return isset($this->$name);
    }

}
