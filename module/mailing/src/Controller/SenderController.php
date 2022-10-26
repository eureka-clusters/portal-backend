<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Application\Form\SearchFilter;
use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Mailing\Entity;
use Mailing\Entity\Sender;
use Mailing\Form\MailingFilter;
use Mailing\Service\MailingService;

use function ceil;
use function sprintf;
use function urlencode;

use const PHP_INT_MAX;

final class SenderController extends MailingAbstractController
{
    public function __construct(private readonly MailingService $mailingService, private readonly FormService $formService, private readonly TranslatorInterface $translator)
    {
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute(param: 'page', default: 1);
        $filterPlugin = $this->getFilter();
        $senderFilter    = $this->mailingService
            ->findFiltered(entity: Sender::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(
            paginator: new ORMPaginator(
            query: $senderFilter,
            fetchJoinCollection: false)));
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber(pageNumber: (int) $page);
        $paginator->setPageRange(pageRange: (int) ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();

        $form->setData(data: $filterPlugin->getFilterFormData());

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
        $sender = $this->mailingService->find(entity: Sender::class, id: (int) $this->params('id'));

        if (null === $sender) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            variables: [
                'sender' => $sender,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Sender $sender */
        $sender = $this->mailingService->find(entity: Sender::class, id: (int) $this->params('id'));
        $data   = $this->getRequest()->getPost()->toArray();
        $form   = $this->formService->prepare(classNameOrEntity: $sender, data: $data);

        if (! $this->mailingService->canDeleteSender(sender: $sender)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/sender/view', params: ['id' => $sender->getId()]);
            }

            if (isset($data['delete']) && $this->mailingService->canDeleteSender(sender: $sender)) {
                $this->mailingService->delete(abstractEntity: $sender);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-sender-%s-has-successfully-been-deleted'),
                        $sender->getSender()
                    )
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/sender/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Sender $sender */
                $sender = $form->getData();
                $this->mailingService->save(entity: $sender);

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/sender/view', params: ['id' => $sender->getId()]);
            }
        }

        return new ViewModel(variables: ['form' => $form, 'sender' => $sender]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: Sender::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/sender/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Sender $sender */
                $sender = $form->getData();
                $this->mailingService->save(entity: $sender);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-sender-%s-has-successfully-been-created'),
                        $sender->getSender()
                    )
                );

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/sender/view', params: ['id' => $sender->getId()]);
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }
}
