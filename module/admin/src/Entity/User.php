<?php

declare(strict_types=1);

namespace Admin\Entity;

use Application\Entity\AbstractEntity;
use Cluster\Entity\Funder;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
    private ?int $id = null;
    /**
     * @ORM\Column(nullable=true)
     */
    private ?string $password = null;
    /**
     * @ORM\Column()
     */
    private string $firstName = '';
    /**
     * @ORM\Column()
     */
    private string $lastName = '';
    /**
     * @ORM\Column(unique=true)
     */
    private string $email = '';
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
    private ?DateTime $dateUpdated = null;
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
     */
    private Collection $roles;
    /**
     * @ORM\OneToMany(targetEntity="Admin\Entity\Session", mappedBy="user")
     */
    private Collection $session;
    /**
     * @ORM\OneToOne(targetEntity="Cluster\Entity\Funder", mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Funder $funder = null;
    /**
     * @ORM\OneToMany(targetEntity="Cluster\Entity\Project\Evaluation", cascade={"persist"}, mappedBy="user")
     */
    private Collection $evaluation;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AccessToken", mappedBy="user", cascade={"persist"})
     */
    private Collection $oAuthAccessTokens;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\AuthorizationCode", mappedBy="user", cascade={"persist"})
     */
    private Collection $oAuthAuthorizationCodes;
    /**
     * @ORM\OneToMany(targetEntity="Api\Entity\OAuth\RefreshToken", mappedBy="user", cascade={"persist"})
     */
    private Collection $oAuthRefreshTokens;

    public function __construct()
    {
        $this->dateCreated = new DateTime();

        $this->roles                   = new ArrayCollection();
        $this->session                 = new ArrayCollection();
        $this->oAuthAccessTokens       = new ArrayCollection();
        $this->oAuthAuthorizationCodes = new ArrayCollection();
        $this->oAuthRefreshTokens      = new ArrayCollection();
        $this->evaluation      = new ArrayCollection();
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

    public function getRoles(): Collection
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): User
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

    public function getDateUpdated(): ?DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(?DateTime $dateUpdated): User
    {
        $this->dateUpdated = $dateUpdated;
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

    public function getSession(): Collection
    {
        return $this->session;
    }

    public function setSession(Collection $session): User
    {
        $this->session = $session;
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

    public function getOAuthAccessTokens(): Collection
    {
        return $this->oAuthAccessTokens;
    }

    public function setOAuthAccessTokens(Collection $oAuthAccessTokens): User
    {
        $this->oAuthAccessTokens = $oAuthAccessTokens;
        return $this;
    }

    public function getOAuthAuthorizationCodes(): Collection
    {
        return $this->oAuthAuthorizationCodes;
    }

    public function setOAuthAuthorizationCodes(Collection $oAuthAuthorizationCodes): User
    {
        $this->oAuthAuthorizationCodes = $oAuthAuthorizationCodes;
        return $this;
    }

    public function getOAuthRefreshTokens(): Collection
    {
        return $this->oAuthRefreshTokens;
    }

    public function setOAuthRefreshTokens(Collection $oAuthRefreshTokens): User
    {
        $this->oAuthRefreshTokens = $oAuthRefreshTokens;
        return $this;
    }

    public function getEvaluation(): Collection
    {
        return $this->evaluation;
    }

    public function setEvaluation(Collection $evaluation): User
    {
        $this->evaluation = $evaluation;
        return $this;
    }


}
