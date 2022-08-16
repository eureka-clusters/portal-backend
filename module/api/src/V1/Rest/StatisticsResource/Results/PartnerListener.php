<?php

declare (strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Service\Project\PartnerService;
use Doctrine\Common\Collections\Criteria;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

use function base64_decode;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly UserService $userService,
        private readonly PartnerProvider $partnerProvider,
        private readonly PartnerYearProvider $partnerYearProvider,
    ) {
    }

    public function fetchAll($params = []): Paginator
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return new Paginator(new ArrayAdapter());
        }

        $encodedFilter = $this->getEvent()->getQueryParams()->get('filter');

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $defaultSort = 'partner.organisation.name';
        $sort = $this->getEvent()->getQueryParams()?->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get('order', strtolower(Criteria::ASC));

        $hasYears = !empty($arrayFilter['year']);

        $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), $arrayFilter, $sort, $order);
        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);

        $doctrineORMAdapter->setProvider($hasYears ? $this->partnerYearProvider : $this->partnerProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
