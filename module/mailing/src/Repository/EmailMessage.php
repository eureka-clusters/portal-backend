<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Jield\Search\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;

use function sprintf;

final class EmailMessage extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select(select: 'mailing_entity_email_message');
        $qb->from(from: Entity\EmailMessage::class, alias: 'mailing_entity_email_message');
        $qb->leftJoin(join: 'mailing_entity_email_message.user', alias: 'admin_entity_user');

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(x: 'admin_entity_user.firstName', y: ':like'),
                    $qb->expr()->like(x: 'admin_entity_user.lastName', y: ':like'),
                    $qb->expr()->like(x: 'admin_entity_user.email', y: ':like'),
                    $qb->expr()->like(x: 'mailing_entity_email_message.emailAddress', y: ':like')
                )
            );
            $qb->setParameter(key: 'like', value: sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy(sort: 'mailing_entity_email_message.id', order: $direction);
                break;
            case 'subject':
                $qb->addOrderBy(sort: 'mailing_entity_email_message.subject', order: $direction);
                break;
            default:
                $qb->addOrderBy(sort: 'mailing_entity_email_message.id', order: Criteria::DESC);
        }

        return $qb;
    }
}
