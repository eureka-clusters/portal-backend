<?php

declare(strict_types=1);

namespace Admin\Controller\OAuth2;

use Admin\Entity\User;
use Admin\Form;
use Admin\Service\OAuth2Service;
use Api\Entity;
use Api\Entity\OAuth\Scope;
use Application\Controller\Plugin\GetFilter;
use Jield\Search\Form\SearchFilter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

use function array_merge;
use function ceil;

/**
 * @method GetFilter getFilter()
 * @method FlashMessenger flashMessenger()
 * @method User identity();
 */
final class ScopeController extends AbstractActionController
{
    public function __construct(
        private readonly OAuth2Service $oAuth2Service,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $form = new SearchFilter();

        $page = $this->params('page');

        $roleQuery = $this->oAuth2Service->findFiltered(entity: Scope::class, formResult: $filterPlugin->getFilter());

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
                'form'      => $form,
                'order'     => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $scope = $this->oAuth2Service->find(entity: Scope::class, id: (int) $this->params('id'));

        if (null === $scope) {
            return $this->notFoundAction();
        }

        return new ViewModel(variables: ['scope' => $scope]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\OAuth2\Scope();
        $form->setData($data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/scope/list'
                );
            }

            if ($form->isValid()) {
                $scope = new Scope();
                $scope->setType(type: $data['type']);
                $scope->setScope(scope: $data['scope']);
                $scope->setIsDefault(isDefault: $data['is_default'] === '1');

                $this->oAuth2Service->save(entity: $scope);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-scope-has-been-created-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/scope/view',
                    params: [
                        'id' => $scope->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\OAuth\Scope $scope */
        $scope = $this->oAuth2Service->find(entity: Scope::class, id: (int) $this->params('id'));

        if (null === $scope) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'type'       => $scope->getType(),
                'scope'      => $scope->getScope(),
                'is_default' => $scope->isDefault(),
            ],
            $this->getRequest()->getPost()->toArray()
        );

        $form = new Form\OAuth2\Scope();
        $form->setData($data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/scope/view',
                    params: [
                        'id' => $scope->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $scope->setType(type: $data['type']);
                $scope->setScope(scope: $data['scope']);
                $scope->setIsDefault(isDefault: $data['is_default'] === '1');

                $this->oAuth2Service->save(entity: $scope);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-user-oauth2-scope-has-been-updated-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/scope/view',
                    params: [
                        'id' => $scope->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }
}
