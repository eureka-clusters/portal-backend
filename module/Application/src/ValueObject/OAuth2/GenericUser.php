<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2021 Jield BV (https://jield.nl)
 */

declare(strict_types=1);

namespace Application\ValueObject\OAuth2;

use Laminas\Json\Json;

/**
 * Class GenericUser
 * @package Application\ValueObject\OAuth2
 */
final class GenericUser
{
    private string $id;
    private string $cluster;
    private array  $clusterPermissions;
    private string $firstName;
    private string $lastName;
    private string $email;
    private bool   $isFunder;
    private array  $funder;
    private array  $address;
    private string $funderCountry;

    public function __construct(\stdClass $result)
    {
        $this->id                 = (string)$result->id;
        $this->firstName          = $result->first_name;
        $this->cluster            = $result->cluster;
        $this->clusterPermissions = (array)($result->cluster_permissions ?? []);
        $this->lastName           = $result->last_name;
        $this->isFunder           = $result->is_funder;
        $this->funder             = (array)($result->funder ?? []);
        $this->address            = (array)($result->address ?? []);
        $this->email              = $result->email;
        $this->funderCountry      = $result->funder_country;
    }

    public static function fromJson(string $jsonString, array $allowedClusters): GenericUser
    {
        // decode as array to be able to use the filter
        $data = Json::decode($jsonString, Json::TYPE_ARRAY);
        // filter the cluster permissions
        $data['cluster_permissions'] = array_intersect($data['cluster_permissions'], $allowedClusters);
        return new self((object)$data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCluster(): string
    {
        return $this->cluster;
    }

    public function getClusterPermissions(): array
    {
        return $this->clusterPermissions;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isFunder(): bool
    {
        return $this->isFunder;
    }

    public function getFunder(): array
    {
        return $this->funder;
    }

    public function getAddress(): array
    {
        return $this->address;
    }

    public function getFunderCountry(): string
    {
        return $this->funderCountry;
    }
}
