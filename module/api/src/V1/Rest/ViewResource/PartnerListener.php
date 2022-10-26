<?php

declare(strict_types=1);

namespace Api\V1\Rest\ViewResource;

use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\ApiTools\Rest\AbstractResourceListener;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly PartnerProvider $partnerProvider
    ) {
    }

    public function fetch($slug = null)
    {
        $partner = $this->partnerService->findPartnerBySlug(slug: $slug);

        if (null === $partner) {
            return new ApiProblem(status: 404, detail: 'The selected partner cannot be found');
        }

        return $this->partnerProvider->generateArray(partner: $partner);
    }
}
