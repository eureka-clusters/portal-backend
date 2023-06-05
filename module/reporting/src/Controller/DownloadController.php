<?php

declare(strict_types=1);

namespace Reporting\Controller;

use Admin\Entity\User;
use Jield\Search\Controller\Plugin\GetFilter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use Reporting\Service\StorageLocationService;

/**
 * @method FlashMessenger flashMessenger()
 * @method User identity()
 * @method GetFilter getFilter()
 */
final class DownloadController extends AbstractActionController
{

    public function __construct(
        private readonly StorageLocationService $storageLocationService,
    ) {
    }

    public function blobAction(): Response|ViewModel
    {
        //Temporary workaround to suppress deprecated warnings
        error_reporting(E_ALL ^ E_DEPRECATED);

        $blobClient = $this->storageLocationService->getBlobService();

        $blob = $blobClient->getBlob(
            container: $this->storageLocationService->getDefaultStorageLocation()->getContainer(),
            blob: $this->params('name')
        );

        /** @var Response $response */
        $response = $this->getResponse();

        $response->setContent(stream_get_contents($blob->getContentStream()));
        $headers = $response->getHeaders();
        $headers->clearHeaders()->addHeaderLine(
            headerFieldNameOrLine: 'Content-Type',
            fieldValue: $blob->getProperties()->getContentType()
        )
            ->addHeaderLine(
                headerFieldNameOrLine: 'Content-Disposition',
                fieldValue: 'attachment;'
            )
            ->addHeaderLine(
                headerFieldNameOrLine: 'Content-Length',
                fieldValue: $blob->getProperties()->getContentLength()
            );

        return $response;
    }

}
