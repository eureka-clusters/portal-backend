<?php

declare(strict_types=1);

namespace Admin\Repository\Api;

use Admin\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;
use function strtoupper;

class Log extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'admin_entity_api_log');
        $qb->from(from: Entity\Api\Log::class, alias: 'admin_entity_api_log');

        if (! empty($filter['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'admin_entity_api_log.payload', y: ':like'),
                    $qb->expr()->like(x: 'admin_entity_api_log.response', y: ':like'),
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $filter['search']));
        }

        $direction = 'DESC';
        if (
            isset($filter['direction'])
            && in_array(needle: strtoupper(string: (string) $filter['direction']), haystack: ['ASC', 'DESC'], strict: true)
        ) {
            $direction = strtoupper(string: (string) $filter['direction']);
        }

        match ($filter['order']) {
            'id' => $qb->addOrderBy(sort: 'admin_entity_api_log.id', order: $direction),
            'date' => $qb->addOrderBy(sort: 'admin_entity_api_log.dateCreated', order: $direction),
            'type' => $qb->addOrderBy(sort: 'admin_entity_api_log.type', order: $direction),
            default => $qb->addOrderBy(sort: 'admin_entity_api_log.id', order: Criteria::DESC),
        };

        return $qb;
    }
}
