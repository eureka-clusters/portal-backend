<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Entity;
use Admin\Entity\Role;
use Admin\Form\RoleFilter;
use Admin\Service\AdminService;
use Application\Controller\Plugin\GetFilter;
use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;

use function ceil;
use function sprintf;

/**
 * @method GetFilter getFilter()
 * @method FlashMessenger flashMessenger()
 */
final class RoleController extends AbstractActionController
{
    public function __construct(
        private readonly AdminService $adminService,
        private readonly FormService $formService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $form = new RoleFilter();

        $page = $this->params('page');

        $roleQuery = $this->adminService->findFiltered(entity: Role::class, formResult: $filterPlugin->getFilter());

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
        $role = $this->adminService->find(entity: Role::class, id: (int) $this->params('id'));

        if (null === $role) {
            return $this->notFoundAction();
        }

        return new ViewModel(variables: ['role' => $role, 'adminService' => $this->adminService]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: Role::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /** @var Entity\Role $role */
            $role = $form->getData();
            $this->adminService->save(entity: $role);

            $this->flashMessenger()->addSuccessMessage(
                message: sprintf(
                    $this->translator->translate(message: "txt-user-role-%s-has-been-created-successfully"),
                    $role->getDescription()
                )
            );

            return $this->redirect()->toRoute(
                route: 'zfcadmin/role/view',
                params: [
                    'id' => $role->getId(),
                ]
            );
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Role $role */
        $role = $this->adminService->find(entity: Role::class, id: (int) $this->params('id'));

        if (null === $role) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: $role, data: $data);

        if (! $this->adminService->canDeleteRole($role)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/role/view', params: ['id' => $role->getId()]);
            }

            if (isset($data['delete']) && $this->adminService->canDeleteRole($role)) {
                $this->adminService->delete(abstractEntity: $role);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: "txt-user-role-%s-has-been-deleted-successfully"),
                        $role->getDescription()
                    )
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/role/list');
            }
            if ($form->isValid()) {
                /** @var Entity\Role $role */
                $role = $form->getData();
                $this->adminService->save(entity: $role);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: "txt-user-role-%s-has-been-updated-successfully"),
                        $role->getDescription()
                    )
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/role/view',
                    params: [
                        'id' => $role->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form, 'role' => $role]);
    }
}
