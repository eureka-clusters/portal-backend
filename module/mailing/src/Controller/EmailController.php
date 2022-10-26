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
use Search\Form\SearchFilter;

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
        $page         = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getFilter();
        $query        = $this->mailingService->findFiltered(EmailMessage::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($query, false)));
        $paginator::setDefaultItemCountPerPage($page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            [
                'paginator'     => $paginator,
                'form'          => $form,
                
                'order'         => $filterPlugin->getOrder(),
                'direction'     => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $emailMessage = $this->mailingService->find(EmailMessage::class, (int) $this->params('id'));
        if (null === $emailMessage) {
            return $this->notFoundAction();
        }

        return new ViewModel(['emailMessage' => $emailMessage]);
    }
}
