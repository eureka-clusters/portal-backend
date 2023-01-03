<?php

declare(strict_types=1);

namespace Api\V1\Rest\ListResource;

use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\OrganisationProvider;
use Cluster\Service\OrganisationService;
use Doctrine\Common\Collections\Criteria;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Paginator\Paginator;

final class OrganisationListener extends AbstractResourceListener
{
    public function __construct(
        private readonly OrganisationService $organisationService,
        private readonly OrganisationProvider $organisationProvider
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $sort  = $params->sort ?? 'name';
        $order = $params->order ?? strtolower(string: Criteria::ASC);

        $organisationQueryBuilder = $this->organisationService->getOrganisations(filter: [], sort: $sort, order: $order);

        $doctrineORMAdapter = new DoctrineORMAdapter(query: $organisationQueryBuilder);
        $doctrineORMAdapter->setProvider(provider: $this->organisationProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
