<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Entity\Project\Partner;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Provider\Project\PartnerYearProvider;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Json\Json;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private readonly PartnerService $partnerService,
        private readonly UserService $userService,
        private readonly TranslatorInterface $translator,
        private readonly PartnerProvider $partnerProvider,
        private readonly PartnerYearProvider $partnerYearProvider
    ) {
    }

    public function fetch($id = 'csv'): array
    {
        $user = $this->userService->findUserById(id: (int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter = $this->getEvent()->getQueryParams()?->get(name: 'filter');
        $filter = base64_decode(string: $filter, true);
        $arrayFilter = Json::decode(encodedValue: $filter, objectDecodeType: Json::TYPE_ARRAY);

        $defaultSort = 'partner.organisation.name';
        $sort = $this->getEvent()->getQueryParams()?->get(name: 'sort', default: $defaultSort);
        $order = $this->getEvent()->getQueryParams()?->get(name: 'order', default: 'asc');

        $partnerQueryBuilder = $this->partnerService->getPartners(
            user: $user,
            filter: $arrayFilter,
            sort: $sort,
            order: $order
        );

        $partners = $partnerQueryBuilder->getQuery()->getResult();

        $results = [];
        if (!empty($arrayFilter['year'])) {
            /** @var Partner $partner */
            foreach ($partners as $partner) {
                $results[] = $this->partnerYearProvider->generateArray(entity: $partner);
            }
        } else {
            /** @var Partner $partner */
            foreach ($partners as $partner) {
                $results[] = $this->partnerProvider->generateArray(partner: $partner);
            }
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle(title: 'Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();
        $partnerSheet->setTitle(title: $this->translator->translate(message: 'txt-partners'));

        $row = 1;
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

        if (!empty($arrayFilter['year'])) {
            $partnerSheet->setCellValue(
                coordinate: $column++ . $row,
                value: $this->translator->translate(
                    message: 'txt-partner-costs-in-year'
                )
            );
            $partnerSheet->setCellValue(
                coordinate: $column . $row,
                value: $this->translator->translate(
                    message: 'txt-partner-effort-in-year'
                )
            );
        } else {
            $partnerSheet->setCellValue(
                coordinate: $column++ . $row,
                value: $this->translator->translate(
                    message: 'txt-partner-costs'
                )
            );
            $partnerSheet->setCellValue(
                coordinate: $column . $row,
                value: $this->translator->translate(
                    message: 'txt-partner-effort'
                )
            );
        }

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['number']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['project']['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['organisation']['name']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['organisation']['country']['country']);
            $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['organisation']['type']['type']);

            if (!empty($arrayFilter['year'])) {
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionCostsInYear']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionEffortInYear']);
            } else {
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionCosts']);
                $partnerSheet->getCell(coordinate: $column++ . $row)->setValue(value: $result['latestVersionEffort']);
            }
        }

        $excelWriter = IOFactory::createWriter(spreadsheet: $spreadSheet, writerType: 'Xlsx');

        ob_start();
        $excelWriter->save(filename: 'php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode(string: $file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
