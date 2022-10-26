<?php

declare(strict_types=1);

namespace Application\Service;

use Application\Entity\AbstractEntity;
use Application\Repository\FilteredObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Jield\Authorize\Role\UserAsRoleInterface;
use Jield\Authorize\Service\HasPermitInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Application\ValueObject\SearchFormResult;

abstract class AbstractService implements HasPermitInterface
{
    public function __construct(
        protected EntityManager $entityManager,
        protected ?TranslatorInterface $translator = null
    ) {
    }

    public function findAll(string $entity): array
    {
        return $this->entityManager->getRepository(entityName: $entity)->findAll();
    }

    public function find(string $entity, int $id): ?AbstractEntity
    {
        return $this->entityManager->getRepository(entityName: $entity)->find(id: $id);
    }

    public function findByName(string $entity, string $column, string $name): ?AbstractEntity
    {
        return $this->entityManager->getRepository(entityName: $entity)->findOneBy(criteria: [$column => $name]);
    }

    public function findFiltered(string $entity, SearchFormResult $formResult): QueryBuilder
    {
        /** @var FilteredObjectRepository $repository */
        $repository = $this->entityManager->getRepository(entityName: $entity);

        if (!in_array(
            needle: FilteredObjectRepository::class,
            haystack: class_implements(object_or_class: $repository),
            strict: true)) {
            throw new \InvalidArgumentException(
                message: sprintf(
                    'The repository of %s should implement %s',
                    $entity,
                    FilteredObjectRepository::class
                )
            );
        }

        return $repository->findFiltered(searchFormResult: $formResult);
    }


    public function save(AbstractEntity $entity): AbstractEntity
    {
        if (!$this->entityManager->contains(entity: $entity)) {
            $this->entityManager->persist(entity: $entity);
        }
        $this->entityManager->flush();

        return $entity;
    }

    public function delete(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->remove(entity: $abstractEntity);
        $this->entityManager->flush();
    }

    public function refresh(AbstractEntity $abstractEntity): void
    {
        $this->entityManager->refresh(entity: $abstractEntity);
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
