<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\ListResource;

use Cluster\Provider\OrganisationProvider;
use Cluster\Rest\Collection\OrganisationCollection;
use Cluster\Service\OrganisationService;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 * Class ProjectListener
 * @package Api\V1\Rest\ListResource
 */
final class OrganisationListener extends AbstractResourceListener
{
    private OrganisationService  $organisationService;
    private OrganisationProvider $organisationProvider;

    public function __construct(OrganisationService $organisationService, OrganisationProvider $organisationProvider)
    {
        $this->organisationService  = $organisationService;
        $this->organisationProvider = $organisationProvider;
    }

    public function fetchAll($params = [])
    {
        $partnerQueryBuilder = $this->organisationService->getOrganisations([]);

        return (new OrganisationCollection($partnerQueryBuilder, $this->organisationProvider))->getItems(
            $params->offset,
            $params->amount ?? 25
        );
    }
}
