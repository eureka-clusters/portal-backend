<?php

declare(strict_types=1);

namespace Mailing\Repository;

use Application\Repository\FilteredObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mailing\Entity;
use Application\ValueObject\SearchFormResult;

use function sprintf;

final class EmailMessage extends EntityRepository implements FilteredObjectRepository
{
    public function findFiltered(SearchFormResult $searchFormResult): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('mailing_entity_email_message');
        $qb->from(Entity\EmailMessage::class, 'mailing_entity_email_message');
        $qb->leftJoin('mailing_entity_email_message.user', 'admin_entity_user');

        if ($searchFormResult->hasQuery()) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('admin_entity_user.firstName', ':like'),
                    $qb->expr()->like('admin_entity_user.lastName', ':like'),
                    $qb->expr()->like('admin_entity_user.email', ':like'),
                    $qb->expr()->like('mailing_entity_email_message.emailAddress', ':like')
                )
            );
            $qb->setParameter('like', sprintf('%%%s%%', $searchFormResult->getQuery()));
        }

        $direction = $searchFormResult->getDirection();

        switch ($searchFormResult->getOrder()) {
            case 'id':
                $qb->addOrderBy('mailing_entity_email_message.id', $direction);
                break;
            case 'subject':
                $qb->addOrderBy('mailing_entity_email_message.subject', $direction);
                break;
            default:
                $qb->addOrderBy('mailing_entity_email_message.id', Criteria::DESC);
        }

        return $qb;
    }
}
