<?php

declare(strict_types=1);

namespace Cluster\Controller;

use Application\Controller\Plugin\GetFilter;
use Cluster\Entity\Project;
use Cluster\Form\ProjectManipulation;
use Cluster\Service\ProjectService;
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
final class ProjectController extends AbstractActionController
{
    public function __construct(private readonly ProjectService $projectService, private readonly TranslatorInterface $translator)
    {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $form = new SearchFilter();

        $page = $this->params('page');

        $projectQuery = $this->projectService->findFiltered(entity: Project::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $projectQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(
            pageRange: ceil(
                num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()
            )
        );

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
        $project = $this->projectService->find(entity: Project::class, id: (int)$this->params('id'));

        if (null === $project) {
            return $this->notFoundAction();
        }

        $form = new ProjectManipulation();
        $data = $this->getRequest()->getPost()->toArray();
        $form->setData(data: $data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $this->projectService->delete(entity: $project);
            $this->flashMessenger()->addSuccessMessage(message: $this->translator->translate(message: 'txt-project-deleted'));

            return $this->redirect()->toRoute(
                route: 'zfcadmin/project/list',
            );
        }

        return new ViewModel(variables: ['project' => $project, 'form' => $form]);
    }
}
