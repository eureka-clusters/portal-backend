<?php

declare(strict_types=1);

namespace Admin\Entity;

use Admin\Entity\User;
use Application\Entity\AbstractEntity;
use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

use function in_array;

/**
 * @ORM\Table(name="admin_role")
 * @ORM\Entity(repositoryClass="Admin\Repository\Role")
 *
 * @Annotation\Name("admin_role")
 */
class Role extends AbstractEntity
{
    public const ROLE_ADMIN  = 1;
    public const ROLE_USER   = 2;
    public const ROLE_PUBLIC = 3;

    public static array $lockedRoles
        = [
            self::ROLE_ADMIN,
            self::ROLE_USER,
            self::ROLE_PUBLIC,
        ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Annotation\Exclude()
     * @var int
     */
    private $id;
    /**
     * @ORM\Column(type="string",length=255,nullable=false)
     *
     * @Annotation\Type("\Laminas\Form\Element\Text")
     * @Annotation\Options({"label":"txt-role-description","help-block":"txt-role-description-help-block"})
     * @var string
     */
    private $description;
    /**
     * @ORM\ManyToMany(targetEntity="Admin\Entity\User", mappedBy="roles", cascade={"persist"})
     *
     * @Annotation\Exclude()
     * @var User[]|Collections\ArrayCollection
     */
    private $users;

    public function __construct()
    {
        $this->users = new Collections\ArrayCollection();
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
        return (string) $this->description;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Role
    {
        $this->description = $description;
        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users): Role
    {
        $this->users = $users;
        return $this;
    }
}
