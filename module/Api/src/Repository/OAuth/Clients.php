<?php

/**
 * ITEA Office all rights reserved
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   Copyright (c) 2021 ITEA Office (https://itea3.org)
 * @license     https://itea3.org/license.txt proprietary
 */

declare(strict_types=1);

namespace Api\Repository\OAuth;

use Api\Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use OAuth2\Storage\ClientCredentialsInterface;

use function in_array;
use function sprintf;

/**
 * Class Clients
 * @package Api\Repository\OAuth
 */
final class Clients extends EntityRepository //implements ClientCredentialsInterface
{
    public function findFiltered(array $filter): QueryBuilder
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('api_entity_oauth_clients');
        $qb->from(Entity\OAuth\Clients::class, 'api_entity_oauth_clients');

        if (null !== $filter) {
            $qb = $this->applyRoleFilter($qb, $filter);
        }

        $direction = Criteria::ASC;
        if (
            isset($filter['direction']) && in_array(
                strtoupper($filter['direction']),
                [
                    Criteria::ASC,
                    Criteria::DESC
                ],
                true
            )
        ) {
            $direction = strtoupper($filter['direction']);
        }

        switch ($filter['order']) {
            case 'id':
                $qb->addOrderBy('api_entity_oauth_clients.id', $direction);
                break;
            default:
                $qb->addOrderBy('api_entity_oauth_clients.id', $direction);
        }

        return $qb;
    }

    public function applyRoleFilter(QueryBuilder $qb, array $filter): QueryBuilder
    {
        if (! empty($filter['query'])) {
            $qb->andWhere($qb->expr()->like('api_entity_oauth_clients.client', ':like'));
            $qb->setParameter('like', sprintf('%%%s%%', $filter['query']));
        }

        return $qb;
    }

//    // functions from bshaffer cookbook https://bshaffer.github.io/oauth2-server-php-docs/cookbook/doctrine2/
//    public function getClientDetails($clientIdentifier)
//    {
//        $client = $this->findOneBy(['client_identifier' => $clientIdentifier]);
//        if ($client) {
//            $client = $client->toArray();
//        }
//        return $client;
//    }
//
//    // function for ClientCredentialsInterface
//    public function checkClientCredentials($clientIdentifier, $clientSecret = NULL)
//    {
//        $client = $this->findOneBy(['client_identifier' => $clientIdentifier]);
//        if ($client) {
//            return $client->verifyClientSecret($clientSecret);
//        }
//        return false;
//    }
//
//    public function checkRestrictedGrantType($clientId, $grantType)
//    {
//        // no support for different grant types per client atm.
//        return true;
//    }
//
//    // function for ClientCredentialsInterface
//    public function isPublicClient($clientId)
//    {
//        return false;
//    }
//
//    public function getClientScope($clientId)
//    {
//        return null;
//    }
}
