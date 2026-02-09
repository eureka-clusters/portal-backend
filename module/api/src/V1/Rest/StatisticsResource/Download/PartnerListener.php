<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Service\Project\PartnerService;
use DateTime;
use DateTimeInterface;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\Json\Json;
use Laminas\Translator\TranslatorInterface;
use OpenApi\Attributes as OA;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class PartnerListener extends AbstractRoutedListener
{
    protected static string $route = '/api/statistics/results/partner/download/:id';

    public function __construct(
        private readonly PartnerService      $partnerService,
        private readonly UserService         $userService,
        private readonly TranslatorInterface $translator,
        private readonly PartnerProvider     $partnerProvider,
        private readonly PartnerYearProvider $partnerYearProvider
    )
    {
    }

    #[OA\Get(
        path: '/api/statistics/results/partner/download/{id}',
        description: 'Download project partners',
        summary: 'Download project partners to Excel',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                description: 'base64 encoded JSON filter',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'eyJ0eXBlIjoiY29udGFjdCIsImNvbnRhY3QiOlt7Im5hbWUiOiJwcm9qZWN0IiwidmFsdWUiOjF9XX0='
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Downloaded file information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'download', type: 'string', example: 'base64 encoded file'),
                        new OA\Property(property: 'extension', type: 'string', example: 'File extension'),
                        new OA\Property(property: 'mimetype', type: 'string', example: 'File mimetype'),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Forbidden'),
        ],
    )]
    #[\Override]
    public function fetch(string $id): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = [];

        //Inject the encoded filter from the results
        $encodedFilter    = base64_decode((string)$id, true);
        $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);

        $searchFormResult = SearchFormResult::fromArray($filter);

        $partnerQueryBuilder = $this->partnerService->getPartners(
            user: $user,
            searchFormResult: $searchFormResult,
        );

        $partners = $partnerQueryBuilder->getQuery()->getResult();

        $results = [];
        if (!empty($filter['filter']['year'])) {
            /** @var Partner $partner */
            foreach ($partners as $partner) {
                $results[] = $this->partnerYearProvider->generateArray(entity: $partner);
            }
        } else {
            /** @var Partner $partner */
            foreach ($partners as $partner) {
                $results[] = $this->partnerProvider->generateArray(entity: $partner);
            }
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle(title: 'Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();
        $partnerSheet->setTitle(title: $this->translator->translate(message: 'txt-partners'));

        // Build header row as an array
        $data   = [];
        $header = [
            $this->translator->translate(message: 'txt-project-number'),
            $this->translator->translate(message: 'txt-project-name'),
            $this->translator->translate(message: 'txt-partner'),
            $this->translator->translate(message: 'txt-country'),
            $this->translator->translate(message: 'txt-partner-type'),
            $this->translator->translate(message: 'txt-is-coordinator'),
            $this->translator->translate(message: 'txt-is-self-funded'),
            $this->translator->translate(message: 'txt-is-active'),
            $this->translator->translate(message: 'txt-primary-cluster'),
            $this->translator->translate(message: 'txt-secondary-cluster'),
            $this->translator->translate(message: 'txt-programme'),
            $this->translator->translate(message: 'txt-programme-call'),
            $this->translator->translate(message: 'txt-label-date'),
            $this->translator->translate(message: 'txt-official-start-date'),
            $this->translator->translate(message: 'txt-official-end-date'),
            $this->translator->translate(message: 'txt-project-status'),
            $this->translator->translate(message: 'txt-project-outline-costs'),
            $this->translator->translate(message: 'txt-project-outline-effort'),
            $this->translator->translate(message: 'txt-full-project-proposal-costs'),
            $this->translator->translate(message: 'txt-full-project-proposal-effort'),
            $this->translator->translate(message: 'txt-latest-version-costs'),
            $this->translator->translate(message: 'txt-latest-version-effort'),
            $this->translator->translate(message: 'txt-latest-version-is-fpp'),
        ];
        $data[] = $header;

        // Build data rows as arrays
        foreach ($results as $result) {
            $labelDate         = null;
            $officialStartDate = null;
            $officialEndDate   = null;

            if (null !== $result['project']['labelDate']) {
                $labelDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['project']['labelDate'])->format(format: 'Y-m-d');
            }

            if (null !== $result['project']['officialStartDate']) {
                $officialStartDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['project']['officialStartDate'])->format(format: 'Y-m-d');
            }

            if (null !== $result['project']['officialEndDate']) {
                $officialEndDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['project']['officialEndDate'])->format(format: 'Y-m-d');
            }

            $data[] = [
                $result['project']['number'],
                $result['project']['name'],
                $result['organisation']['name'],
                $result['organisation']['country']['country'],
                $result['organisation']['type']['type'],
                $result['isCoordinator'],
                $result['isSelfFunded'],
                $result['isActive'],
                $result['project']['primaryCluster']['name'] ?? null,
                $result['project']['secondaryCluster']['name'] ?? null,
                $result['project']['programme'],
                $result['project']['programmeCall'],
                $labelDate,
                $officialStartDate,
                $officialEndDate,
                $result['project']['status']['status'] ?? null,
                $result['projectOutlineCosts'],
                $result['projectOutlineEffort'],
                $result['fullProjectProposalCosts'],
                $result['fullProjectProposalEffort'],
                $result['latestVersionCosts'],
                $result['latestVersionEffort'],
                (null === $result['project']['latestVersion'] ? null : $result['project']['latestVersion']['isLatestVersionAndIsFPP']),
            ];
        }

        // Write the entire dataset to the sheet starting at A1
        $partnerSheet->fromArray($data);

        $excelWriter = IOFactory::createWriter(spreadsheet: $spreadSheet, writerType: 'Xlsx');

        ob_start();
        $excelWriter->save(filename: 'php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode(string: $file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
