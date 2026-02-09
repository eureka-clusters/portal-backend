<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Entity\User;
use Admin\Service\UserService;
use Api\Listener\AbstractRoutedListener;
use Cluster\Entity\Project;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use DateTime;
use DateTimeInterface;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\Json\Json;
use Laminas\Translator\TranslatorInterface;
use OpenApi\Attributes as OA;
use Override;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class ProjectListener extends AbstractRoutedListener
{
    protected static string $route = '/api/statistics/results/project/download/:id';

    public function __construct(
        private readonly ProjectService      $projectService,
        private readonly UserService         $userService,
        private readonly TranslatorInterface $translator,
        private readonly ProjectProvider     $projectProvider
    )
    {
    }

    #[OA\Get(
        path: '/api/statistics/results/project/download/{id}',
        description: 'Download projects',
        summary: 'Download projects to Excel',
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
    #[Override]
    public function fetch(string $id): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (!$user instanceof User) {
            return [];
        }

        $filter = [];

        //Inject the encoded filter from the results
        $encodedFilter    = base64_decode((string)$id, true);
        $filter['filter'] = Json::decode(encodedValue: $encodedFilter, objectDecodeType: Json::TYPE_ARRAY);

        $searchFormResult = SearchFormResult::fromArray($filter);

        $projectQueryBuilder = $this->projectService->getProjects(
            user: $user,
            searchFormResult: $searchFormResult,
        );

        $projects = $projectQueryBuilder->getQuery()->getResult();

        $results = [];
        /** @var Project $project */
        foreach ($projects as $project) {
            $results[] = $this->projectProvider->generateArray(entity: $project);
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle(title: 'Statistics');
        $projectSheet = $spreadSheet->getActiveSheet();

        $projectSheet->setTitle(title: $this->translator->translate(message: 'txt-projects'));

        // Build header row as an array
        $data   = [];
        $header = [
            $this->translator->translate(message: 'txt-project-number'),
            $this->translator->translate(message: 'txt-project-name'),
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
            $this->translator->translate(message: 'txt-involved-countries'),
        ];
        $data[] = $header;

        // Build data rows as arrays
        foreach ($results as $result) {
            $labelDate         = null;
            $officialStartDate = null;
            $officialEndDate   = null;

            if (null !== $result['labelDate']) {
                $labelDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['labelDate'])->format(format: 'Y-m-d');
            }

            if (null !== $result['officialStartDate']) {
                $officialStartDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['officialStartDate'])->format(format: 'Y-m-d');
            }

            if (null !== $result['officialEndDate']) {
                $officialEndDate = DateTime::createFromFormat(format: DateTimeInterface::ATOM, datetime: $result['officialEndDate'])->format(format: 'Y-m-d');
            }

            $countries = [];
            foreach ($result['countries'] as $countryData) {
                $countries[] = $countryData['iso3'];
            }

            $data[] = [
                $result['number'],
                $result['name'],
                $result['primaryCluster']['name'] ?? null,
                $result['secondaryCluster']['name'] ?? null,
                $result['programme'],
                $result['programmeCall'],
                $labelDate,
                $officialStartDate,
                $officialEndDate,
                $result['status']['status'] ?? null,
                $result['projectOutlineCosts'],
                $result['projectOutlineEffort'],
                $result['fullProjectProposalCosts'],
                $result['fullProjectProposalEffort'],
                $result['latestVersionCosts'],
                $result['latestVersionEffort'],
                $result['latestVersion']['isLatestVersionAndIsFPP'] ?? false,
                implode(separator: ', ', array: $countries),
            ];
        }

        // Write the entire dataset to the sheet starting at A1
        $projectSheet->fromArray($data);

        $excelWriter = IOFactory::createWriter(spreadsheet: $spreadSheet, writerType: 'Xlsx');

        ob_start();
        $excelWriter->save(filename: 'php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode(string: $file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
