<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Entity\Project;
use Cluster\Provider\ProjectProvider;
use Cluster\Service\ProjectService;
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
        private readonly ProjectService $projectService,
        private readonly UserService $userService,
        private readonly TranslatorInterface $translator,
        private readonly ProjectProvider $projectProvider
    ) {
    }

    #[OA\Get(
        path: '/api/statistics/results/project/download',
        description: 'Download projects',
        summary: 'Download projects to Excel',
        tags: ['Project'],
        parameters: [
            new OA\Parameter(
                name: 'filter',
                description: 'base64 encoded JSON filter',
                in: 'query',
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
    public function fetchAll($params = []): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        if (null === $user) {
            return [];
        }

        $filter = $params->toArray();

        //Inject the encoded filter from the results
        if (isset($params->filter)) {
            $filter           = base64_decode(string: $params->filter, strict: true);
            $filter['filter'] = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);
        }

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
        $partnerSheet = $spreadSheet->getActiveSheet();

        $partnerSheet->setTitle(title: $this->translator->translate(message: 'txt-projects'));

        $row    = 1;
        $column = 'A';
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-number'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-name'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-primary-cluster'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-secondary-cluster'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-start-date'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-official-end-date'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-duration-(months)'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-status'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-total-costs'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-total-effort'
            )
        );

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['number']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['primaryCluster']['name'] ?? null
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['secondaryCluster']['name'] ?? null
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['officialStartDate'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['officialEndDate'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['duration']['months'] ?? null
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['status']['status'] ?? null);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionTotalCosts']);
            $partnerSheet->getCell(coordinate: $column . $row)->setValue(value: $result['latestVersionTotalEffort']);
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
