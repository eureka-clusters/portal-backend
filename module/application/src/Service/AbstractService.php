<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\AbstractEntity;
use Doctrine\ORM\EntityManager;
use Jield\Authorize\Role\UserAsRoleInterface;
use Jield\Authorize\Service\HasPermitInterface;
use Laminas\I18n\Translator\TranslatorInterface;

abstract class AbstractService implements HasPermitInterface
{
    public function __construct(
        protected EntityManager $entityManager,
        protected ?TranslatorInterface $translator = null
    ) {
    }

    public function findAll(string $entity): array
    {
        return $this->entityManager->getRepository($entity)->findAll();
    }

    public function find(string $entity, int $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entity)->find($id);
    }

    public function findByName(string $entity, string $column, string $name): ?AbstractEntity
    {
        return $this->entityManager->getRepository($entity)->findOneBy([$column => $name]);
    }

    public function save(AbstractEntity $entity): AbstractEntity
    {
        if (!$this->entityManager->contains($entity)) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->remove($abstractEntity);
        $this->entityManager->flush();
    }

    public function refresh(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->refresh($abstractEntity);
    }

    public function hasPermit(UserAsRoleInterface $user, object $resource, array|string $privilege): bool
    {
        return true;
    }

    public function hasGeneralPermit(UserAsRoleInterface $user, string $className, string $privilege): bool
    {
        return true;
    }

}
