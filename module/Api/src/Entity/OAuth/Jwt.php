<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Entity\OAuth;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oauth_jwt")
 * @ORM\Entity
 */
class Jwt extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer",nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    /**
     * @ORM\Column(name="client_id", type="string")
     */
    private string $clientId;
    /**
     * @ORM\Column(name="subject", length=80, type="string")
     */
    private string $subject;
    /**
     * @ORM\Column(name="public_key", length=2000, type="string")
     */
    private string $publicKey;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Jwt
    {
        $this->id = $id;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): Jwt
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Jwt
    {
        $this->subject = $subject;
        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): Jwt
    {
        $this->publicKey = $publicKey;
        return $this;
    }
}
