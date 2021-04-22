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
 * Class Log
 *
 * @package Admin\Repository
 */
class Log extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_log');
        $qb->from(Entity\Log::class, 'admin_entity_log');

        if (!empty($filter['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_log.event', ':like'),
                    $qb->expr()->like('admin_entity_log.url', ':like'),
                    $qb->expr()->like('admin_entity_log.file', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $filter['search']));
        }

        $direction = 'DESC';
        if (isset($filter['direction'])
            && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_log.id', $direction);
                break;
            case 'date':
                $qb->addOrderBy('admin_entity_log.date', $direction);
                break;
            case 'event':
                $qb->addOrderBy('admin_entity_log.event', $direction);
                break;
            default:
                $qb->addOrderBy('admin_entity_log.id', Criteria::DESC);
        }

        return $qb;
    }

    public function truncateLog(): void
    {
        $truncateQuery = 'TRUNCATE TABLE admin_log';
        $this->getEntityManager()->getConnection()->executeQuery($truncateQuery);
    }
}
