<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Mailing\Entity\EmailMessage;
use Mailing\Service\MailingService;
use Application\Form\SearchFilter;

use function ceil;
use function urlencode;

use const PHP_INT_MAX;

final class EmailController extends MailingAbstractController
{
    public function __construct(private readonly MailingService $mailingService, private readonly EntityManager $entityManager)
    {
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute(param: 'page', default: 1);
        $filterPlugin = $this->getFilter();
        $query        = $this->mailingService->findFiltered(entity: EmailMessage::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(
            paginator: new ORMPaginator(
            query: $query,
            fetchJoinCollection: false)));
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(pageRange: ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator'     => $paginator,
                'form'          => $form,
                
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $emailMessage = $this->mailingService->find(entity: EmailMessage::class, id: (int) $this->params('id'));
        if (null === $emailMessage) {
            return $this->notFoundAction();
        }

        return new ViewModel(variables: ['emailMessage' => $emailMessage]);
    }
}
