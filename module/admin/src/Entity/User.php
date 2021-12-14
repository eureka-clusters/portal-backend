<?php

declare(strict_types=1);

namespace Admin\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Api\Entity\OAuth\AccessToken;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Funder;
use DateTime;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;

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
     * @ORM\Column(nullable=true)
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
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $dateCreated;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="update")
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
     * @var Role[]|Collections\Collection
     */
    private array|Collection $roles;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", mappedBy="user")
     *
     * @var Session[]|Collections\Collection
     */
    private array|Collection $session;
    /**
     * @ORM\OneToOne(targetEntity="Cluster\Entity\Funder", mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Funder $funder = null;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AccessToken", mappedBy="user", cascade={"persist"})
     *
     * @var AccessToken[]|Collections\Collection
     */
    private array|Collection $oAuthAccessTokens;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AuthorizationCode", mappedBy="user", cascade={"persist"})
     */
    private array|Collection $oAuthAuthorizationCodes;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\RefreshToken", mappedBy="user", cascade={"persist"})
     */
    private array|Collection $oAuthRefreshTokens;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\Jwt", mappedBy="user", cascade={"persist"})
     */
    private array|Collection $oAuthJwt;

    #[Pure] public function __construct()
    {
        $this->roles                   = new ArrayCollection();
        $this->session                 = new ArrayCollection();
        $this->oAuthAccessTokens       = new ArrayCollection();
        $this->oAuthAuthorizationCodes = new ArrayCollection();
        $this->oAuthRefreshTokens      = new ArrayCollection();
        $this->oAuthJwt                = new ArrayCollection();
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
                static fn($key, Role $role) => $role->getId() === $userRole->getId()
            );
    }

    public function getRoles(): ArrayCollection|array
    {
        return $this->roles;
    }

    public function setRoles($roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    public function isFunder(): bool
    {
        return null !== $this->funder;
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

    public function getSession(): array|Collection
    {
        return $this->session;
    }

    public function setSession($session): User
    {
        $this->session = $session;
        return $this;
    }

    public function getOAuthAccessTokens(): array|Collection
    {
        return $this->oAuthAccessTokens;
    }

    public function setOAuthAccessTokens($oAuthAccessTokens): User
    {
        $this->oAuthAccessTokens = $oAuthAccessTokens;
        return $this;
    }

    public function getOAuthAuthorizationCodes(): Collection|array
    {
        return $this->oAuthAuthorizationCodes;
    }

    public function setOAuthAuthorizationCodes($oAuthAuthorizationCodes): User
    {
        $this->oAuthAuthorizationCodes = $oAuthAuthorizationCodes;
        return $this;
    }

    public function getOAuthRefreshTokens(): ArrayCollection|array
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

    public function getOAuthJwt(): Collection|array
    {
        return $this->oAuthJwt;
    }

    public function setOAuthJwt(Collection|array $oAuthJwt): User
    {
        $this->oAuthJwt = $oAuthJwt;
        return $this;
    }
}
