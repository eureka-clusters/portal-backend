<?php

declare(strict_types=1);

namespace Admin\Entity;

use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Laminas\Form\Annotation;
use Laminas\Form\Element\Text;
use Laminas\Permissions\Acl\Role\RoleInterface;

use function in_array;

#[ORM\Table(name: 'admin_role')]
#[ORM\Entity(repositoryClass: \Admin\Repository\Role::class)]
#[Annotation\Name('admin_role')]
class Role extends AbstractEntity implements RoleInterface
{
    public final const ROLE_ADMIN = 1;
    public final const ROLE_USER = 2;
    public final const ROLE_PUBLIC = 3;

    public static array $lockedRoles
        = [
            self::ROLE_ADMIN,
            self::ROLE_USER,
            self::ROLE_PUBLIC,
        ];

    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Annotation\Exclude]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Annotation\Type(Text::class)]
    #[Annotation\Options(['label' => 'txt-role-description', 'help-block' => 'txt-role-description-help-block'])]
    private string $description = '';

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles', cascade: ['persist'])]
    #[Annotation\Exclude]
    private Collection $users;

    public function getRoleId(): string
    {
        return strtolower((string)$this->id);
    }

    #[Pure] public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function isLocked(): bool
    {
        return in_array($this->id, self::$lockedRoles, true);
    }

    public function isUser(): bool
    {
        return $this->id === self::ROLE_USER;
    }

    public function __toString(): string
    {
        return $this->description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Role
    {
        $this->id = $id;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Role
    {
        $this->description = $description;
        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): Role
    {
        $this->users = $users;
        return $this;
    }
}
