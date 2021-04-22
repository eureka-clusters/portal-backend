<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Application\Service;

use Admin\Entity;
use Application\Entity\AbstractEntity;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Laminas\I18n\Translator\TranslatorInterface;

use function get_class;

/**
 * Class AbstractService
 *
 * @package Program\Service
 */
abstract class AbstractService
{
    protected EntityManager $entityManager;
    protected ?TranslatorInterface $translator;

    public function __construct(EntityManager $entityManager, TranslatorInterface $translator = null)
    {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
    }

    public function findFilteredByUser(
        string $entity,
        array $filter,
        Entity\User $user,
        string $permission = 'list'
    ): QueryBuilder {
        //The 'filter' should always be there to support the repositories
        if (!array_key_exists('filter', $filter)) {
            $filter['filter'] = [];
        }

        $qb = $this->findFiltered($entity, $filter);

        return $this->limitQueryBuilderByPermissions($qb, $user, $entity, $permission);
    }

    public function findFiltered(string $entity, array $filter): QueryBuilder
    {
        return $this->entityManager->getRepository($entity)->findFiltered(
            $filter,
            AbstractQuery::HYDRATE_SIMPLEOBJECT
        );
    }

    protected function limitQueryBuilderByPermissions(
        QueryBuilder $qb,
        Entity\User $user,
        string $entity,
        string $permit = 'list'
    ): QueryBuilder {
        //Create an entity from the name
        /** @var AbstractEntity $entity */
        $entity = new $entity();

        switch ($permit) {
            case 'edit':
                $limitQueryBuilder = $this->parseWherePermit($entity, 'edit', $user);
                break;
            case 'list':
            default:
                $limitQueryBuilder = $this->parseWherePermit($entity, 'list', $user);
                break;
        }


        /*
         * Limit the programs based on the rights
         */
        if (null !== $limitQueryBuilder) {
            $qb->andWhere(
                $qb->expr()
                    ->in(strtolower($entity->get('underscore_entity_name')), $limitQueryBuilder->getDQL())
            );
        } else {
            $qb->andWhere(
                $qb->expr()->isNull(
                    strtolower($entity->get('underscore_entity_name'))
                    . '.id'
                )
            );
        }

        return $qb;
    }

    public function parseWherePermit(AbstractEntity $entity, string $roleName, Entity\User $user): ?QueryBuilder
    {
        $permitEntity = $this->findPermitEntityByEntity($entity);

        if (null === $permitEntity) {
            throw new InvalidArgumentException(sprintf('Entity "%s" cannot be found as permit', get_class($entity)));
        }

        //Try to find the corresponding role
        $role = $this->entityManager->getRepository(Entity\Permit\Role::class)->findOneBy(
            [
                'entity' => $permitEntity,
                'role'   => $roleName,
            ]
        );


        if (null === $role) {
            //We have no roles found, so return a query which gives always zeros
            //We will simply return NULL
            print sprintf('<br><br><br>role "%s" on entity "%s" could not be found<br><hr>', $roleName, $entity);

            return null;
        }

        //@todo; fix this when no role is found (equals to NULL for example)
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('permit_user.keyId');
        $qb->from(Entity\Permit\User::class, 'permit_user');
        $qb->andWhere('permit_user.user = ' . $user->getId());
        $qb->andWhere('permit_user.role = ' . $role->getId());

        return $qb;
    }

    public function findPermitEntityByEntity(AbstractEntity $entity): ?Entity\Permit\Entity
    {
        return $this->entityManager->getRepository(Entity\Permit\Entity::class)
            ->findOneBy(['underscoreFullEntityName' => $entity->get('underscore_entity_name')]);
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

        $this->flushPermitsByEntityAndId($entity, (int)$entity->getId());

        return $entity;
    }

    public function flushPermitsByEntityAndId(AbstractEntity $entity, int $id): void
    {
        $permitEntity = $this->findPermitEntityByEntity($entity);
        /**
         * Do not do anything when the permit cannot be found
         */
        if (null === $permitEntity) {
            return;
        }

        $repository = $this->entityManager->getRepository(Entity\Permit\Entity::class);
        $repository->flushPermitsByEntityAndId($permitEntity, $id);

        $this->flushAccessPermitsByEntityAndId($permitEntity, $id);
    }

    private function flushAccessPermitsByEntityAndId(Entity\Permit\Entity $permitEntity, int $id): void
    {
        /**
         * Add the role based on the role_selections
         */
        foreach ($permitEntity->getRole() as $role) {
            foreach ($role->getAccessRole() as $accessRole) {
                $this->flushPermitsPerRoleByAccessRoleAndId($role, $accessRole, $id);
            }
        }
    }

    private function flushPermitsPerRoleByAccessRoleAndId(
        Entity\Permit\Role $permitRole,
        Entity\Role $role,
        int $id
    ): void {
        /**
         * Go over te users in the selection
         */
        foreach ($role->getUsers() as $user) {
            $repository = $this->entityManager->getRepository(Entity\Permit\Role::class);

            $repository->insertPermitsForRoleByUserAndId($permitRole, $user, $id, $role);
        }
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
}
