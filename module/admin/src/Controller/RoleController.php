<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Entity;use Admin\Entity\Role;use Admin\Form\RoleFilter;use Admin\Service\AdminService;use Application\Controller\Plugin\GetFilter;use Application\Controller\Plugin\Preferences;use Application\Service\FormService;use Doctrine\Common\Collections\ArrayCollection;use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;use Laminas\Http\Response;use Laminas\I18n\Translator\TranslatorInterface;use Laminas\Mvc\Controller\AbstractActionController;use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;use Laminas\Paginator\Paginator;use Laminas\View\Model\ViewModel;use function ceil;use function sprintf;

/**
 * final class RoleController
 *
 * @method GetFilter getFilter()
 * @method Preferences preferences()
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

        $roleQuery = $this->adminService->findFiltered(Role::class, $filterPlugin->getFilter());

        $paginator = new Paginator(
            new PaginatorAdapter(paginator: new ORMPaginator($roleQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage($this->preferences()->getItemsPerPage());
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            [
                'paginator' => $paginator,
                'form' => $form,

                'order' => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $role = $this->adminService->find(Role::class, (int)$this->params('id'));

        if (null === $role) {
            return $this->notFoundAction();
        }

        return new ViewModel(['role' => $role, 'adminService' => $this->adminService]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Role::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /** @var Entity\Role $role */
            $role = $form->getData();
            $this->adminService->save($role);

            $this->flashMessenger()->addSuccessMessage(
                sprintf(
                    $this->translator->translate("txt-user-role-%s-has-been-created-successfully"),
                    $role->getDescription()
                )
            );

            return $this->redirect()->toRoute(
                'zfcadmin/role/view',
                [
                    'id' => $role->getId(),
                ]
            );
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Role $role */
        $role = $this->adminService->find(Role::class, (int)$this->params('id'));

        if (null === $role) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($role, $data);

        if (!$this->adminService->canDeleteRole($role)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/role/view', ['id' => $role->getId()]);
            }

            if (isset($data['delete']) && $this->adminService->canDeleteRole($role)) {
                $this->adminService->delete($role);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-user-role-%s-has-been-deleted-successfully"),
                        $role->getDescription()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/role/list');
            }
            if ($form->isValid()) {
                /** @var Entity\Role $role */
                $role = $form->getData();

                if (!isset($data['admin_entity_role']['selection'])) {
                    $role->setSelection(new ArrayCollection());
                }

                $this->adminService->save($role);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-user-role-%s-has-been-updated-successfully"),
                        $role->getDescription()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/role/view',
                    [
                        'id' => $role->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'role' => $role]);
    }
}
