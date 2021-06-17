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
    private string $firstName;
    private string $lastName;
    private string $email;
    private bool   $isFunder;
    private array  $funder;
    private array  $address;

    public function __construct(\stdClass $result)
    {
        $this->id        = (string)$result->id;
        $this->firstName = $result->first_name;
        $this->cluster   = $result->cluster;
        $this->lastName  = $result->last_name;
        $this->isFunder  = $result->is_funder;
        $this->funder    = (array)($result->funder ?? []);
        $this->address   = (array)($result->address ?? []);
        $this->email     = $result->email;
    }

    public static function fromJson(string $jsonString): GenericUser
    {
        return new self(Json::decode($jsonString));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCluster(): string
    {
        return $this->cluster;
    }

    public function setCluster($cluster): GenericUser
    {   
        $this->cluster = $cluster;
        return $this;
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

}
