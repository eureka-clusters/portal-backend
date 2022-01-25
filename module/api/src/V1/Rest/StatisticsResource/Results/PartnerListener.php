<?php

declare (strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Results;

use Admin\Service\UserService;
use Api\Paginator\DoctrineORMAdapter;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;

use function base64_decode;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private PartnerService $partnerService,
        private UserService $userService,
        private PartnerProvider $partnerProvider
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
        $filter      = base64_decode($encodedFilter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);


        $defaultorder = 'asc';
        $defaultSort = 'partner.organisation.name';
        $sort = $this->getEvent()->getQueryParams()->get('sort', $defaultSort);
        $order = $this->getEvent()->getQueryParams()->get('order', 'asc');

        $partnerQueryBuilder = $this->partnerService->getPartners($user->getFunder(), $arrayFilter, $sort, $order);

        if (!empty($arrayFilter['year'])) {
            $partnerYears = [];
            //We need to pepare the parnters so we get results per year
            foreach ($partnerQueryBuilder->getQuery()->getResult() as $partner) {
                foreach ($arrayFilter['year'] as $year) {
                    $partnerYears[] = array_merge(
                        $this->partnerProvider->generateArray($partner),
                        $this->partnerProvider->generateYearArray($partner, (int)$year),
                        ['keyfield' => sprintf('%s_%d', $partner->getId(), $year)]
                    );
                }
            }

            return new Paginator(new ArrayAdapter($partnerYears));
        }

        $doctrineORMAdapter = new DoctrineORMAdapter($partnerQueryBuilder);
        $doctrineORMAdapter->setProvider($this->partnerProvider);

        return new Paginator($doctrineORMAdapter);
    }
}
