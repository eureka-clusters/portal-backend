<?php

declare(strict_types=1);

namespace Admin\Entity;

use Api\Entity\OAuth\AccessToken;
use Api\Entity\OAuth\AuthorizationCode;
use Api\Entity\OAuth\RefreshToken;
use Application\Entity\AbstractEntity;
use Cluster\Entity\Funder;
use Cluster\Entity\Project\Evaluation;
use DateTime;
use Deeplink\Entity\Deeplink;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use DoctrineORMModule\Form\Element\EntityMultiCheckbox;
use Gedmo\Mapping\Annotation as Gedmo;
use Jield\Authorize\Role\UserAsRoleInterface;
use Laminas\Form\Annotation;
use Laminas\Form\Annotation\Exclude;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Mailing\Entity\EmailMessage;
use function sprintf;


#[ORM\Table(name: 'admin_user')]
#[ORM\Entity(repositoryClass: \Admin\Repository\User::class)]
class User extends AbstractEntity implements UserAsRoleInterface
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Annotation\Type(Hidden::class)]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Annotation\Exclude]
    private ?string $password = null;

    #[ORM\Column]
    #[Annotation\Type(Text::class)]
    #[Annotation\Options(['help-block' => 'txt-user-first-name-help-block'])]
    #[Annotation\Attributes([
        'label'       => 'txt-user-first-name-label',
        'placeholder' => 'txt-user-first-name-placeholder'
    ])]
    private string $firstName = '';

    #[ORM\Column]
    #[Annotation\Type(Text::class)]
    #[Annotation\Options(['help-block' => 'txt-user-last-name-help-block'])]
    #[Annotation\Attributes([
        'label'       => 'txt-user-last-name-label',
        'placeholder' => 'txt-user-last-name-placeholder'
    ])]
    private string $lastName = '';

    #[ORM\Column(unique: true)]
    #[Annotation\Type(\Laminas\Form\Element\Email::class)]
    #[Annotation\Options(['help-block' => 'txt-user-email-address-help-block'])]
    #[Annotation\Attributes([
        'label'       => 'txt-user-email-address-label',
        'placeholder' => 'txt-user-email-address-placeholder'
    ])]
    private string $email = '';

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Gedmo\Timestampable(on: 'create')]
    #[Annotation\Exclude]
    private DateTime $dateCreated;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update')]
    #[Annotation\Exclude]
    private ?DateTime $dateUpdated = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Annotation\Exclude]
    private ?DateTime $dateEnd = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Annotation\Type(\Laminas\Form\Element\Checkbox::class)]
    #[Annotation\Options(['help-block' => 'txt-user-is-eureka-secretariat-staff-member-help-block'])]
    #[Annotation\Attributes([
        'label' => 'txt-user-is-eureka-secretariat-staff-member-label',
    ])]
    private bool $isEurekaSecretariatStaffMember = false;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', cascade: ['persist'], fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(value: ['description' => Criteria::ASC])]
    #[ORM\JoinTable(name: 'admin_user_role')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\InverseJoinColumn(nullable: false)]
    #[Annotation\Type(EntityMultiCheckbox::class)]
    #[Annotation\Options(['target_class' => Role::class, 'help-block' => 'txt-user-roles-help-block'])]
    #[Annotation\Attributes(['label' => 'txt-user-role-label'])]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Session::class)]
    #[Annotation\Exclude]
    private Collection $session;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Funder::class, cascade: ['persist', 'remove'])]
    #[Annotation\Exclude]
    private ?Funder $funder = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Evaluation::class, cascade: ['persist'])]
    #[Annotation\Exclude]
    private Collection $evaluation;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccessToken::class, cascade: ['persist'])]
    #[Annotation\Exclude]
    private Collection $oAuthAccessTokens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AuthorizationCode::class, cascade: ['persist'])]
    #[Annotation\Exclude]
    private Collection $oAuthAuthorizationCodes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RefreshToken::class, cascade: ['persist'])]
    #[Annotation\Exclude]
    private Collection $oAuthRefreshTokens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Deeplink::class, cascade: ['persist', 'remove'])]
    #[Exclude]
    private Collection $deeplink;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EmailMessage::class, cascade: ['persist', 'remove'])]
    #[Exclude]
    private Collection $emailMessage;

    public function __construct()
    {
        $this->dateCreated = new DateTime();

        $this->roles                   = new ArrayCollection();
        $this->session                 = new ArrayCollection();
        $this->oAuthAccessTokens       = new ArrayCollection();
        $this->oAuthAuthorizationCodes = new ArrayCollection();
        $this->oAuthRefreshTokens      = new ArrayCollection();
        $this->evaluation              = new ArrayCollection();
        $this->deeplink                = new ArrayCollection();
        $this->emailMessage            = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->parseFullName();
    }

    public function addRoles(Collection $roles): void
    {
        foreach ($roles as $role) {
            $this->roles->add($role);
        }
    }

    public function removeRoles(Collection $roles): void
    {
        foreach ($roles as $role) {
            $this->roles->removeElement($role);
        }
    }

    public function parseFullName(): string
    {
        if (empty($this->firstName) || empty($this->lastName)) {
            return $this->email;
        }

        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getUserId(): string
    {
        return 'user' . $this->id;
    }

    public function getRolesAsArray(): array
    {
        $roles = [Role::ROLE_USER];
        foreach ($this->roles as $role) {
            $roles[] = $role->getId();
        }

        return $roles;
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

    public function hasRole(Role $userRole): bool
    {
        return null !== $this->getRoles()
            && $this->getRoles()->exists(
                p: static fn($key, Role $role) => $role->getId() === $userRole->getId()
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

    public function isEurekaSecretariatStaffMember(): bool
    {
        return $this->isEurekaSecretariatStaffMember;
    }

    public function setIsEurekaSecretariatStaffMember(bool $isEurekaSecretariatStaffMember): User
    {
        $this->isEurekaSecretariatStaffMember = $isEurekaSecretariatStaffMember;
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

    public function getDeeplink(): Collection
    {
        return $this->deeplink;
    }

    public function setDeeplink(Collection $deeplink): User
    {
        $this->deeplink = $deeplink;
        return $this;
    }

    public function getEmailMessage(): Collection
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(Collection $emailMessage): User
    {
        $this->emailMessage = $emailMessage;
        return $this;
    }
}
