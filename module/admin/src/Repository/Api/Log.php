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
        $qb->select('admin_entity_api_log');
        $qb->from(Entity\Api\Log::class, 'admin_entity_api_log');

        if (! empty($filter['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_api_log.payload', ':like'),
                    $qb->expr()->like('admin_entity_api_log.response', ':like'),
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = 'DESC';
        if (
            isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_api_log.id', $direction);
                break;
            case 'date':
                $qb->addOrderBy('admin_entity_api_log.dateCreated', $direction);
                break;
            case 'type':
                $qb->addOrderBy('admin_entity_api_log.type', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_api_log.id', Criteria::DESC);
        }

        return $qb;
    }
}
