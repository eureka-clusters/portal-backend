<?php

declare(strict_types=1);

namespace Admin\Controller\OAuth2;

use Admin\Entity\User;
use Admin\Form\OAuth2\UpdateServiceClientSecretForm;
use Admin\Form\OAuth2\UpdateServicePrivateKeyForm;
use Admin\Service\oAuth2Service;
use Api\Entity;
use Api\Entity\OAuth\Service;
use Api\Enum\OAuth2\ServiceTypeEnum;
use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use GuzzleHttp\Exception\RequestException;
use Jield\Search\Controller\Plugin\GetFilter;
use Jield\Search\Form\SearchFilter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\Translator\TranslatorInterface;
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
        private readonly oAuth2Service       $oAuth2Service,
        private readonly FormService         $formService,
        private readonly TranslatorInterface $translator
    )
    {
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

        $form->setData(data: $filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator'    => $paginator,
                'form'         => $form,
                'order'        => $filterPlugin->getOrder(),
                'direction'    => $filterPlugin->getDirection(),
                'serviceTypes' => ServiceTypeEnum::cases(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        /** @var Service $service */
        $service = $this->oAuth2Service->findServiceById(id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $accessToken = null;
        $error       = null;
        $hasTest     = false;
        $success     = false;

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
            'service'     => $service,
            'hasTest'     => $hasTest,
            'success'     => $success,
            'error'       => $error,
            'accessToken' => $accessToken,
        ]);
    }

    public function updateClientSecretAction(): ViewModel|Response
    {
        /** @var Service $service */
        $service = $this->oAuth2Service->findServiceById(id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = new UpdateServiceClientSecretForm();
        $form->setData(data: $data);

        if ($this->getRequest()->isPost()) {
            //Do a request with the service
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $service->setClientSecret(clientSecret: $data['clientSecret']);
                $this->oAuth2Service->save(entity: $service);

                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-service-client-secret-has-been-updated-successfully"
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

        return new ViewModel(variables: [
            'service' => $service,
            'form'    => $form,
        ]);
    }

    public function updatePrivateKeyAction(): ViewModel|Response
    {
        /** @var Service $service */
        $service = $this->oAuth2Service->findServiceById(id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = new UpdateServicePrivateKeyForm();
        $form->setData(data: $data);

        if ($this->getRequest()->isPost()) {
            //Do a request with the service
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $service->setPrivateKey(privateKey: $data['privateKey']);
                $this->oAuth2Service->save(entity: $service);

                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-service-private-key-has-been-updated-successfully"
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

        return new ViewModel(variables: [
            'service' => $service,
            'form'    => $form,
        ]);
    }

    public function newAction(): Response|ViewModel
    {
        $serviceType = ServiceTypeEnum::from(value: (int)$this->params('serviceType'));

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: Service::class, data: $data);

        //Set the required form fields as required
        foreach ($serviceType->getRequiredFormFields() as $requiredFormField) {
            $form->getInputFilter()->get(name: 'api_entity_oauth_service')->get(
                name: $requiredFormField
            )->setRequired(
                required: true
            );
        }

        $form->remove(elementOrFieldset: 'delete');
        $form->setData(data: $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/list'
                );
            }

            if ($form->isValid()) {
                /** @var Service $service */
                $service = $form->getData();
                $service->setType(type: $serviceType);

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

        return new ViewModel(variables: [
            'form'                  => $form,
            'serviceType'           => $serviceType,
            'serviceTypeFormFields' => $serviceType->getFormFields()
        ]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Service $service */
        $service = $this->oAuth2Service->findServiceById(id: (int)$this->params('id'));

        if (null === $service) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: $service, data: $data);

        //Set the required form fields as required
        foreach ($service->getType()->getRequiredFormFields() as $requiredFormField) {
            $form->getInputFilter()->get(name: 'api_entity_oauth_service')->get(
                name: $requiredFormField
            )->setRequired(
                required: true
            );
        }

        //We don't want to edit the secrets here, so we remove the form element
        $form->get(elementOrFieldset: 'api_entity_oauth_service')->remove(elementOrFieldset: 'clientSecret');
        $form->get(elementOrFieldset: 'api_entity_oauth_service')->remove(elementOrFieldset: 'privateKey');
        $form->getInputFilter()->get(name: 'api_entity_oauth_service')->get(name: 'clientSecret')->setRequired(
            required: false
        );
        $form->getInputFilter()->get(name: 'api_entity_oauth_service')->get(name: 'privateKey')->setRequired(
            required: false
        );

        $form->setData(data: $data);

        if (!$this->oAuth2Service->canDeleteService(service: $service)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/service/view',
                    params: [
                        'id' => $service->getId(),
                    ]
                );
            }

            if (isset($data['delete']) && $this->oAuth2Service->canDeleteService(service: $service)) {
                $this->oAuth2Service->delete(entity: $service);

                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-service-has-been-deleted-successfully"
                    ),
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/oauth2/service/list');
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

        return new ViewModel(variables: [
            'form'                       => $form,
            'cannotDeleteServiceReasons' => $this->oAuth2Service->cannotDeleteServiceReasons(
                service: $service
            ),
            'serviceType'                => $service->getType(),
            'service'                    => $service,
        ]);
    }
}
