<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Service\Project\PartnerService;
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

final class PartnerListener extends AbstractResourceListener
{
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
        path: '/api/statistics/results/partner/download/{filter}',
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
    public function fetch($id): array
    {
        $user = $this->userService->findUserById(
            id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']
        );

        $filter = [];

        //Inject the encoded filter from the results
        $encodedFilter    = base64_decode($id, true);
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
                message: 'txt-partner'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-country'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-partner-type'
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
                message: 'txt-programme'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-programme-call'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-label-date'
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
                message: 'txt-project-status'
            )
        );

        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-project-outline-costs'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-project-outline-effort'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-full-project-proposal-costs'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-full-project-proposal-effort'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-latest-version-costs'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column . $row,
            value: $this->translator->translate(
                message: 'txt-latest-version-effort'
            )
        );
        $partnerSheet->setCellValue(
            coordinate: $column++ . $row,
            value: $this->translator->translate(
                message: 'txt-latest-version-is-fpp'
            )
        );

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['number']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['organisation']['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['organisation']['country']['country']
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['organisation']['type']['type']
            );

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['project']['primaryCluster']['name'] ?? null
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                value: $result['project']['secondaryCluster']['name'] ?? null
            );
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['programme']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['programmeCall']);

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

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $labelDate);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $officialStartDate);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $officialEndDate);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['status']['status'] ?? null);

            if (!empty($filter['filter']['year'])) {
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                    value: $result['latestVersionCostsInYear']
                );
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(
                    value: $result['latestVersionEffortInYear']
                );
            } else {
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['projectOutlineCosts']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['projectOutlineEffort']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['fullProjectProposalCosts']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['fullProjectProposalEffort']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionCosts']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionEffort']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['latestVersion']['isLatestVersionAndIsFPP']);
            }
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
