<?php

declare(strict_types=1);

namespace Api\V1\Rest\StatisticsResource\Download;

use Admin\Service\UserService;
use Cluster\Provider\Project\PartnerProvider;
use Cluster\Rest\Collection\PartnerCollection;
use Cluster\Service\Project\PartnerService;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\I18n\Translator\TranslatorInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

use function base64_decode;
use function base64_encode;
use function json_decode;
use function ob_get_clean;
use function ob_start;

use const JSON_THROW_ON_ERROR;

final class PartnerListener extends AbstractResourceListener
{
    private PartnerService $partnerService;
    private UserService $userService;
    private TranslatorInterface $translator;
    private PartnerProvider $partnerProvider;

    public function __construct(
        PartnerService $partnerService,
        UserService $userService,
        TranslatorInterface $translator,
        PartnerProvider $partnerProvider
    ) {
        $this->partnerService  = $partnerService;
        $this->userService     = $userService;
        $this->translator      = $translator;
        $this->partnerProvider = $partnerProvider;
    }

    public function fetch($filter = null)
    {
        $user = $this->userService->findUserById((int) $this->getIdentity()?->getName());

        if (null === $user || ! $user->isFunder()) {
            return [];
        }

        //The filter is a base64 encoded serialised json string
        $filter      = base64_decode($filter);
        $arrayFilter = json_decode($filter, true, 512, JSON_THROW_ON_ERROR);

        // @johan same question as in the ProjectListener
        $partners = $this->partnerService->getPartners($user->getFunder(), $arrayFilter);
        $results  = (new PartnerCollection($partners, $this->partnerProvider))->getItems(
            null,
            null
        );

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
        $partnerSheet->setCellValue($column++ . $row, $this->translator->translate('txt-partner-costs'));
        $partnerSheet->setCellValue($column . $row, $this->translator->translate('txt-partner-effort'));

        foreach ($results as $result) {
            $column = 'A';
            $row++;

            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['number']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['name']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['name']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['country']['country']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['organisation']['type']['type']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['latestVersionTotalCosts']);
            $partnerSheet->getCell($column++ . $row)->setValue($result['project']['latestVersionTotalEffort']);
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
