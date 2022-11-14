<?php

declare(strict_types=1);

namespace Admin\Controller\OAuth2;

use Admin\Entity\User;
use Admin\Service\OAuth2Service;
use Api\Entity;
use Api\Entity\OAuth\Service;
use Application\Controller\Plugin\GetFilter;
use Application\Form\SearchFilter;
use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use GuzzleHttp\Exception\RequestException;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

use function ceil;

/**
 * @method GetFilter getFilter()
 * @method FlashMessenger flashMessenger()
 * @method User identity();
 */
final class ServiceController extends AbstractActionController
{
    public function __construct(
        private readonly OAuth2Service $oAuth2Service,
        private readonly FormService $formService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $form = new SearchFilter();

        $page = $this->params('page');

        $roleQuery = $this->oAuth2Service->findFiltered(entity: Service::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $roleQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(count: 25);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(
            pageRange: ceil(
                num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()
            )
        );

        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
                'form' => $form,
                'order' => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        /** @var Service $service */
        $service = $this->oAuth2Service->find(entity: Service::class, id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $accessToken = null;
        $error = null;
        $hasTest = false;
        $success = false;

        if ($this->getRequest()->isPost()) {
            //Do a request with the service
            $hasTest = true;

            try {
                $accessToken = $this->oAuth2Service->fetchAccessTokenFromService(service: $service);

                $success = true;
            } catch (RequestException $e) {
                $error = $e->getMessage();
            }
        }

        return new ViewModel(variables: [
            'service' => $service,
            'hasTest' => $hasTest,
            'success' => $success,
            'error' => $error,
            'accessToken' => $accessToken,
        ]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: Service::class, data: $data);
        $form->remove('delete');
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/list'
                );
            }

            if ($form->isValid()) {
                /** @var Service $service */
                $service = $form->getData();
                ;

                $this->oAuth2Service->save(entity: $service);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-service-has-been-created-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\OAuth\Service $service */
        $service = $this->oAuth2Service->find(entity: Service::class, id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: $service, data: $data);
        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /** @var Service $service */
                $service = $form->getData();

                $this->oAuth2Service->save(entity: $service);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-service-has-been-updated-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }
}
