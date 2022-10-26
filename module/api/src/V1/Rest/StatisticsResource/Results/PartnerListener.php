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
        $user = $this->userService->findUserById(id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return new Paginator(adapter: new ArrayAdapter());
        }

        $encodedFilter = $this->getEvent()->getQueryParams()?->get(name: 'filter');

        //The filter is a base64 encoded serialised json string
        $filter = base64_decode(string: $encodedFilter);
        $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

        $defaultSort = 'partner.organisation.name';
        $sort = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get(name: 'order', default: strtolower(string: Criteria::ASC));

        $hasYears = !empty($arrayFilter['year']);

        $partnerQueryBuilder = $this->partnerService->getPartners(
            user: $user,
            filter: $arrayFilter,
            sort: $sort,
            order: $order
        );
        $doctrineORMAdapter = new DoctrineORMAdapter(query: $partnerQueryBuilder);

        $doctrineORMAdapter->setProvider(provider: $hasYears ? $this->partnerYearProvider : $this->partnerProvider);

        return new Paginator(adapter: $doctrineORMAdapter);
    }
}
