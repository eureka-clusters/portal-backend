<?php

declare(strict_types=1);

namespace Application\ValueObject\OAuth2;

use Laminas\Json\Json;
use stdClass;

use function array_intersect;

final class GenericUser
{
    private string  $id;
    private string  $cluster;
    private array   $clusterPermissions;
    private string  $firstName;
    private string  $lastName;
    private string  $email;
    private bool    $isFunder;
    private array   $funder;
    private array   $address;
    private ?string $funderCountry;

    public function __construct(stdClass $result)
    {
        $this->id                 = (string)$result->id;
        $this->firstName          = $result->firstName;
        $this->cluster            = $result->cluster;
        $this->clusterPermissions = (array)($result->clusterPermissions ?? []);
        $this->lastName           = $result->lastName;
        $this->isFunder           = $result->isFunder;
        $this->funder             = (array)($result->funder ?? []);
        $this->address            = (array)($result->address ?? []);
        $this->email              = $result->email;
        $this->funderCountry      = $result->funderCountry;
    }

    public static function fromJson(string $jsonString, array $allowedClusters): GenericUser
    {
        // decode as array to be able to use the filter
        $data = Json::decode($jsonString, Json::TYPE_ARRAY);
        // filter the cluster permissions
        $data['clusterPermissions'] = array_intersect($data['clusterPermissions'] ?? [], $allowedClusters);
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

    public function getFunderCountry(): ?string
    {
        return $this->funderCountry;
    }
}
