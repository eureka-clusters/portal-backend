<?php

declare(strict_types=1);

namespace Reporting\Controller;

use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Jield\Search\Form\SearchFilter;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Mailing\Controller\MailingAbstractController;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use Reporting\Entity\StorageLocation;
use Reporting\Service\StorageLocationService;

use function ceil;
use function sprintf;

use const PHP_INT_MAX;

final class StorageLocationController extends MailingAbstractController
{
    public function __construct(
        private readonly StorageLocationService $storageLocationService,
        private readonly FormService $formService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute(param: 'page', default: 1);
        $filterPlugin = $this->getFilter();
        $userQuery    = $this->storageLocationService
            ->findFiltered(entity: StorageLocation::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $userQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber(pageNumber: (int)$page);
        $paginator->setPageRange(
            pageRange: (int)ceil(
                num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()
            )
        );

        $form = new SearchFilter();
        $form->setData(data: $filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
                'form'      => $form,
                'order'     => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel|Response
    {
        error_reporting(error_level: E_ALL ^ E_DEPRECATED);
        $storageLocation = $this->storageLocationService->findStorageLocationById(id: (int)$this->params('id'));

        if (null === $storageLocation) {
            return $this->notFoundAction();
        }

        $hasAccessTested = false;
        $hasAccess       = false;
        $accessMessage   = null;

        if ($this->getRequest()->isPost()) {
            $hasAccessTested = true;
            try {
                $listBlobsOptions = new ListBlobsOptions();
                $listBlobsOptions->setPrefix($storageLocation->getExcelFolder() . '/');

                $this->storageLocationService->getBlobService()->listBlobs(
                    container: $storageLocation->getContainer(),
                    options: $listBlobsOptions

                );
                $hasAccess = true;
            } catch (\Exception $e) {
                $accessMessage = $e->getMessage();
                $hasAccess     = false;
            }
        }

        return new ViewModel(
            variables: [
                'storageLocation' => $storageLocation,
                'hasAccessTested' => $hasAccessTested,
                'hasAccess'       => $hasAccess,
                'accessMessage'   => $accessMessage,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var StorageLocation $storageLocation */
        $storageLocation = $this->storageLocationService->findStorageLocationById(id: (int)$this->params('id'));
        $data            = $this->getRequest()->getPost()->toArray();
        $form            = $this->formService->prepare(classNameOrEntity: $storageLocation, data: $data);

        if (!$this->storageLocationService->canDeleteStorageLocation(storageLocation: $storageLocation)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/reporting/storage-location/view', params: [
                        'id' => $storageLocation->getId()
                    ]);
            }

            if (isset($data['delete']) && $this->storageLocationService->canDeleteStorageLocation(
                    storageLocation: $storageLocation
                )) {
                $this->storageLocationService->delete(abstractEntity: $storageLocation);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-storage-location-%s-has-successfully-been-deleted'),
                        $storageLocation->getName()
                    )
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/reporting/storage-location/list');
            }

            if ($form->isValid()) {
                /** @var StorageLocation $storageLocation */
                $storageLocation = $form->getData();
                $this->storageLocationService->save(entity: $storageLocation);

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/reporting/storage-location/view', params: [
                        'id' => $storageLocation->getId()
                    ]);
            }
        }

        return new ViewModel(
            variables: [
                'form'            => $form,
                'storageLocation' => $storageLocation,
            ]
        );
    }

    public function newAction(): Response|ViewModel
    {
        $service = (int)$this->params('service');

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: StorageLocation::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/reporting/storage-location/list');
            }

            if ($form->isValid()) {
                /** @var StorageLocation $storageLocation */
                $storageLocation = $form->getData();
                $this->storageLocationService->save(entity: $storageLocation);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-storage-location-%s-has-successfully-been-created'),
                        $storageLocation->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/reporting/storage-location/view', params: [
                        'id' => $storageLocation->getId()
                    ]);
            }
        }

        return new ViewModel(
            variables: [
                'form'    => $form,
                'service' => $service,
            ]
        );
    }
}
