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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use function in_array;
use function sprintf;

/**
 * Class Template
 *
 * @package Admin\Repository
 */
final class Template extends EntityRepository
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('admin_entity_template');
        $qb->from(Entity\Template::class, 'admin_entity_template');

        if (null !== $filter) {
            $qb = $this->applyTemplateFilter($qb, $filter);
        }

        $direction = 'ASC';
        if (isset($filter['direction']) && in_array(strtoupper($filter['direction']), ['ASC', 'DESC'], true)) {
            $direction = strtoupper($filter['direction']);
        }


        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('admin_entity_template.id', $direction);
                break;
            case 'template':
                $qb->addOrderBy('admin_entity_template.template', $direction);
                break;
            case 'content':
                $qb->addOrderBy('admin_entity_template.content', $direction);
                break;
            case 'last-update':
                $qb->addOrderBy('admin_entity_template.lastUpdate', $direction);
                break;


            default:
                $qb->addOrderBy('admin_entity_template.template', $direction);
        }

        return $qb;
    }

    public function applyTemplateFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (!empty($filter['query'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_template.template', ':like'),
                    $qb->expr()->like('admin_entity_template.content', ':like')
                )
            );

            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }
}
