<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Entity;

use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\AuthorizationCode;
use Api\Entity\OAuth\Clients;
use Api\Entity\OAuth\RefreshToken;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Funder;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * @ORM\Table(name="admin_user")
 * @ORM\Entity(repositoryClass="Admin\Repository\User")
 */
class User extends AbstractEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;
    /**
     * @ORM\Column()
     */
    private string $password;
    /**
     * @ORM\Column()
     */
    private string $firstName;
    /**
     * @ORM\Column()
     */
    private string $lastName;
    /**
     * @ORM\Column(unique=true)
     */
    private string $email;
    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $dateCreated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $lastUpdate = null;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $dateEnd = null;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Role", inversedBy="users", cascade={"persist"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"description"="ASC"})
     * @ORM\JoinTable(name="admin_user_role",
     *      joinColumns={@ORM\JoinColumn(nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(nullable=false)}
     * )
     *
     * @var Role[]|Collections\ArrayCollection
     */
    private $roles;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", mappedBy="user")
     *
     * @var Session[]|Collections\ArrayCollection
     */
    private $session;
    /**
     * @ORM\OneToOne(targetEntity="Cluster\Entity\Funder", mappedBy="user")
     */
    private ?Funder $funder;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AccessToken", mappedBy="user", cascade={"persist"})
     * @var AccessToken[]|Collections\ArrayCollection
     */
    private $oAuthAccessTokens;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AuthorizationCode", mappedBy="user", cascade={"persist"})
     * @var AuthorizationCode[]|Collections\ArrayCollection
     */
    private $oAuthAuthorizationCodes;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\Clients", mappedBy="user", cascade={"persist"})
     * @var Clients[]|Collections\ArrayCollection
     */
    private $oAuthClients;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\RefreshToken", mappedBy="user", cascade={"persist"})
     * @var RefreshToken[]|Collections\ArrayCollection
     */
    private $oAuthRefreshTokens;

    public function __construct()
    {
        $this->roles   = new Collections\ArrayCollection();
        $this->session = new Collections\ArrayCollection();

        $this->oAuthAccessTokens       = new Collections\ArrayCollection();
        $this->oAuthAuthorizationCodes = new Collections\ArrayCollection();
        $this->oAuthClients            = new Collections\ArrayCollection();
        $this->oAuthRefreshTokens      = new Collections\ArrayCollection();
    }

    public function getRolesAsArray(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getId();
        }

        return $roles;
    }

    public function hasRole(Role $userRole): bool
    {
        return null !== $this->getRoles()
            && $this->getRoles()->exists(
                static function ($key, Role $role) use ($userRole) {
                    return $role->getId() === $userRole->getId();
                }
            );
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(DateTime $dateCreated): User
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getLastUpdate(): ?DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?DateTime $lastUpdate): User
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?DateTime $dateEnd): User
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setSession($session): User
    {
        $this->session = $session;
        return $this;
    }

    public function getOAuthAccessTokens()
    {
        return $this->oAuthAccessTokens;
    }

    public function setOAuthAccessTokens($oAuthAccessTokens): User
    {
        $this->oAuthAccessTokens = $oAuthAccessTokens;
        return $this;
    }

    public function getOAuthAuthorizationCodes()
    {
        return $this->oAuthAuthorizationCodes;
    }

    public function setOAuthAuthorizationCodes($oAuthAuthorizationCodes): User
    {
        $this->oAuthAuthorizationCodes = $oAuthAuthorizationCodes;
        return $this;
    }

    public function getOAuthClients()
    {
        return $this->oAuthClients;
    }

    public function setOAuthClients($oAuthClients): User
    {
        $this->oAuthClients = $oAuthClients;
        return $this;
    }

    public function getOAuthRefreshTokens()
    {
        return $this->oAuthRefreshTokens;
    }

    public function setOAuthRefreshTokens($oAuthRefreshTokens): User
    {
        $this->oAuthRefreshTokens = $oAuthRefreshTokens;
        return $this;
    }

    public function getFunder(): ?Funder
    {
        return $this->funder;
    }

    public function setFunder(?Funder $funder): User
    {
        $this->funder = $funder;
        return $this;
    }
}
