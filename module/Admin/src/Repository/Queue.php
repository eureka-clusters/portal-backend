<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Admin\Repository;

use Admin\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;

/**
 * Class Queue
 * @package Admin\Repository
 */
class Queue extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_queue');
        $qb->from(Entity\Queue::class, 'admin_entity_queue');

        if (!empty($filter['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_queue.queue', ':like'),
                    $qb->expr()->like('admin_entity_queue.data', ':like'),
                    $qb->expr()->like('admin_entity_queue.status', ':like'),
                    $qb->expr()->like('admin_entity_queue.message', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = Criteria::ASC;
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), [Criteria::ASC, Criteria::DESC], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_queue.id', $direction);
                break;
            case 'queue':
                $qb->addOrderBy('admin_entity_queue.queue', $direction);
                break;
            case 'status':
                $qb->addOrderBy('admin_entity_queue.status', $direction);
                break;
            case 'priority':
                $qb->addOrderBy('admin_entity_queue.priority', $direction);
                break;
            case 'created':
                $qb->addOrderBy('admin_entity_queue.created', $direction);
                break;
            case 'scheduled':
                $qb->addOrderBy('admin_entity_queue.scheduled', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_queue.id', Criteria::DESC);
        }

        return $qb;
    }

    public function truncateQueue(): void
    {
        $truncateQuery = 'TRUNCATE TABLE queue_default';
        $this->getEntityManager()->getConnection()->executeQuery($truncateQuery);
    }
}
