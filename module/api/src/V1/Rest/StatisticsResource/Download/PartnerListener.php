<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Json\Json;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Laminas\Paginator\Adapter\ArrayAdapter;

use function base64_decode;
use function base64_encode;
use function ob_get_clean;
use function ob_start;

final class PartnerListener extends AbstractResourceListener
{
    public function __construct(
        private PartnerService $partnerService,
        private UserService $userService,
        private TranslatorInterface $translator,
        private PartnerProvider $partnerProvider
    ) {
    }

    public function fetch($filter = null)
    {
        $user = $this->userService->findUserById((int)$this->getIdentity()?->getAuthenticationIdentity()['user_id']);

        if (null === $user || !$user->isFunder()) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($filter);
        $arrayFilter = Json::decode($filter, Json::TYPE_ARRAY);

        $partners = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);

        if (!empty($arrayFilter['year'])) {
            $partnerYears = [];
            //We need to pepare the parnters so we get results per year
            foreach ($partners as $partner) {
                foreach ($arrayFilter['year'] as $year) {
                    $partnerYears[] = array_merge(
                        $this->partnerProvider->generateArray($partner),
                        $this->partnerProvider->generateYearArray($partner, (int) $year)
                    );
                }
            }

            $results = (new ArrayAdapter($partnerYears))->getItems(
                null,
                null
            );
        } else {
            $results = (new PartnerCollection($partners, $this->partnerProvider))->getItems(
                null,
                null
            );
        }

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getProperties()->setTitle('Statistics');
        $partnerSheet = $spreadSheet->getActiveSheet();
        $partnerSheet->setTitle($this->translator->translate('txt-partners'));

        $row    = 1;
        $column = 'A';
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-number'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-project-name'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-country'));
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-type'));

        if (!empty($arrayFilter['year'])) {
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-costs-in-year'));
            $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-partner-effort-in-year'));
        } else {
            $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-costs'));
            $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-partner-effort'));
        }

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['number']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['name']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['name']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['country']['country']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['type']['type']);

            if (!empty($arrayFilter['year'])) {
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionTotalCostsInYear']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionTotalEffortInYear']);
            } else {
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionCosts']);
                $partnerSheet->getCell($column++ . $row)->setValue($result['latestVersionEffort']);
            }
        }

        $excelWriter = IOFactory::createWriter($spreadSheet, 'Xlsx');

        ob_start();
        $excelWriter->save('php://output');
        $file = ob_get_clean();

        $extension = '.xlsx';
        $mimetype  = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        return ['download' => base64_encode($file), 'extension' => $extension, 'mimetype' => $mimetype];
    }
}
