<?php

declare(strict_types=1);

namespace Application\ValueObject\OAuth2;

use Laminas\Json\Json;
use stdClass;

use function array_intersect;

final class GenericUser
{
    private readonly string $id;

    private readonly string $cluster;

    private readonly array $clusterPermissions;

    private readonly string $firstName;

    private readonly string $lastName;

    private readonly string $email;

    private readonly bool $isFunder;

    private readonly bool $isEurekaSecretariatStaffMember;

    private readonly array $funder;

    private readonly array $address;

    private readonly ?string $funderCountry;

    public function __construct(stdClass $result)
    {
        $this->id = (string)$result->id;
        $this->firstName = $result->firstName;
        $this->cluster = $result->cluster;
        $this->clusterPermissions = (array)($result->clusterPermissions ?? []);
        $this->lastName = $result->lastName;
        $this->isFunder = $result->isFunder;

        //Take the value from the result, fallback to false in case the setting cannot be found
        $this->isEurekaSecretariatStaffMember = $result->isEurekaSecretariatStaffMember ?? false;

        $this->funder = (array)($result->funder ?? []);
        $this->address = (array)($result->address ?? []);
        $this->email = $result->email;
        $this->funderCountry = $result->funderCountry;
    }

    public static function fromJson(string $jsonString, array $allowedClusters): GenericUser
    {
        // decode as array to be able to use the filter
        $data = Json::decode(encodedValue: $jsonString, objectDecodeType: Json::TYPE_ARRAY);
        // filter the cluster permissions
        $data['clusterPermissions'] = array_intersect($data['clusterPermissions'] ?? [], $allowedClusters);
        return new self(result: (object)$data);
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

    public function isEurekaSecretariatStaffMember()
    {
        return $this->isEurekaSecretariatStaffMember;
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
