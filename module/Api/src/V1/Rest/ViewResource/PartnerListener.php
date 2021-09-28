<?php

/**
 * Jield BV all rights reserved
 *
 * @author      Dr. ir. Johan van der Heide <info@jield.nl>
 * @copyright   Copyright (c) 2020 Jield BV (https://jield.nl)
 */

namespace Api\V1\Rest\ViewResource;

use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

/**
 *
 */
final class PartnerListener extends AbstractResourceListener
{
    private PartnerService  $partnerService;
    private PartnerProvider $partnerProvider;

    public function __construct(PartnerService $partnerService, PartnerProvider $partnerProvider)
    {
        $this->partnerService  = $partnerService;
        $this->partnerProvider = $partnerProvider;
    }

    public function fetch($id = null)
    {
        $partner = $this->partnerService->findPartnerByIdentifier($id);

        if (null === $partner) {
            return new ApiProblem(404, 'The selected partner cannot be found');
        }

        return $this->partnerProvider->generateArray($partner);
    }
}
