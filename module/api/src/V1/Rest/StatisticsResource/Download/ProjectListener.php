<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Entity\Project;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
use DateTime;
use DateTimeInterface;
use Jield\Search\ValueObject\SearchFormResult;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Json\Json;
use OpenApi\Attributes as OA;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class ProjectListener extends AbstractResourceListener
{
    public function __construct(
        private readonly ProjectService      $projectService,
        private readonly UserService         $userService,
        private readonly TranslatorInterface $translator,
        private readonly ProjectProvider     $projectProvider
    )
    {
    }

    #[OA\Get(
        path: '/api/statistics/results/project/download/{filter}',
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
    public function fetch($id): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (null === $user) {
            return [];
        }

        $filter = [];

        //Inject the encoded filter from the results
        $encodedFilter    = base64_decode(string: $id, strict: true);
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

        $row    = 1;
        $column = 'A';
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-number'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-name'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-primary-cluster'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-secondary-cluster'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-label-date'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-start-date'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-end-date'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-status'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-total-costs'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-total-effort'
            )
        );
        $projectSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-involved-countries'
            )
        );

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['number']);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['name']);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['primaryCluster']['name'] ?? null
            );
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['secondaryCluster']['name'] ?? null
            );

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

            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $labelDate);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $officialStartDate);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $officialEndDate);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['status']['status'] ?? null);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionTotalCosts']);
            $projectSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionTotalEffort']);

            $countries = [];
            foreach ($result['countries'] as $countryData) {
                $countries[] = $countryData['iso3'];
            }

            $projectSheet->getCell(coordinate: $column . $row)->setValue(value: implode(separator: ', ', array: $countries));

        }

        $excelWriter = IOFactory::createWriter(spreadsheet: $spreadSheet, writerType: 'Xlsx');

        ob_start();
        $excelWriter->save(filename: 'php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode(string: $file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
