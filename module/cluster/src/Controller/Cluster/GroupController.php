<?php

declare(strict_types=1);

namespace Cluster\Controller\Cluster;

use Application\Controller\Plugin\GetFilter;
use Application\Service\FormService;
use Cluster\Entity\Cluster\Group;
use Cluster\Service\ClusterService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Jield\Search\Form\SearchFilter;
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
 */
final class GroupController extends AbstractActionController
{
    public function __construct(
        private readonly ClusterService      $clusterService,
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

        $groupQuery = $this->clusterService->findFiltered(entity: Group::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $groupQuery, fetchJoinCollection: false))
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
        $group = $this->clusterService->findClusterGroupById((int)$this->params('id'));

        if (null === $group) {
            return $this->notFoundAction();
        }

        return new ViewModel(variables: ['group' => $group]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: Group::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost() && $form->isValid()) {
            /** @var Group $group */
            $group = $form->getData();

            $this->clusterService->save(entity: $group);

            $this->flashMessenger()->addSuccessMessage(
                message: $this->translator->translate(message: "txt-cluster-group-has-been-created-successfully")
            );

            return $this->redirect()->toRoute(
                route: 'zfcadmin/cluster/group/view',
                params: [
                    'id' => $group->getId(),
                ]
            );
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Group $group */
        $group = $this->clusterService->findClusterGroupById((int)$this->params('id'));

        if (null === $group) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: $group, data: $data);

        if (!$this->clusterService->canDeleteClusterGroup($group)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/cluster/group/view', params: ['id' => $group->getId()]);
            }

            if (isset($data['delete']) && $this->clusterService->canDeleteClusterGroup($group)) {
                $this->clusterService->delete(entity: $group);

                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(message: "txt-cluster-group-has-been-deleted-successfully")
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/cluster/group/list');
            }
            if ($form->isValid()) {
                /** @var Group $group */
                $group = $form->getData();

                if (empty($data['cluster_entity_cluster_group']['clusters'])) {
                    $group->setClusters(new ArrayCollection());
                }

                $this->clusterService->save(entity: $group);

                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(message: "txt-cluster-group-has-been-updated-successfully")
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/cluster/group/view',
                    params: [
                        'id' => $group->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form, 'group' => $group]);
    }
}
