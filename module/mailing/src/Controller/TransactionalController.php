<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Admin\Entity\User;
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
use Mailing\Entity;
use Mailing\Entity\Transactional;
use Mailing\Form\TransactionalFilter;
use Mailing\Service\EmailService;
use Mailing\Service\MailingService;

use function ceil;
use function sprintf;

/**
 * @method GetFilter getFilter()
 * @method FlashMessenger flashMessenger()
 * @method User identity()
 */
final class TransactionalController extends AbstractActionController
{
    public function __construct(
        private readonly MailingService $mailingService,
        private readonly EmailService $emailService,
        private readonly FormService $formService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $form = new TransactionalFilter();

        $page = $this->params('page');

        $transactionalQuery = $this->mailingService->findFiltered(
            entity: Transactional::class,
            formResult: $filterPlugin->getFilter()
        );

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(
                paginator: new ORMPaginator(
                    query: $transactionalQuery,
                    fetchJoinCollection: false
                )
            )
        );
        $paginator::setDefaultItemCountPerPage(count: 25);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(pageRange: ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form->setData(data: $filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
                'form' => $form,

                'order' => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): Response|ViewModel
    {
        /** @var Entity\Transactional $transactional */
        $transactional = $this->mailingService->find(entity: Transactional::class, id: (int)$this->params('id'));

        if (null === $transactional) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            $email = $this->emailService->createNewTransactionalEmailBuilder(transactionalOrKey: $transactional);
            $email->setSender(setSender: null, ownerOrLoggedInUser: $this->identity());
            $email->addUserTo(user: $this->identity());

            if ($this->emailService->send(emailBuilder: $email)) {
                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(
                            message: 'txt-transactional-email-with-name-%s-has-been-sent-successfully'
                        ),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/mailing/transactional/view',
                    params: [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['transactional' => $transactional]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: Transactional::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/transactional/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Transactional $transactional */
                $transactional = $form->getData();
                $this->mailingService->save(entity: $transactional);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-transactional-%s-has-been-created-successfully'),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/mailing/transactional/view',
                    params: [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Transactional $transactional */
        $transactional = $this->mailingService->find(entity: Transactional::class, id: (int)$this->params('id'));

        if (null === $transactional) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(classNameOrEntity: $transactional, data: $data);

        if (!$this->mailingService->canDeleteTransactional(transactional: $transactional)) {
            $form->remove(elementOrFieldset: 'delete');

            $form->get(elementOrFieldset: 'mailing_entity_transactional')->get(elementOrFieldset: 'key')->setAttribute(
                key: 'disabled',
                value: 'disabled'
            );
            $form->getInputFilter()->get(name: 'mailing_entity_transactional')->get(name: 'key')->setRequired(
                required: false
            );
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete']) && $this->mailingService->canDeleteTransactional(transactional: $transactional)) {
                $this->mailingService->delete(abstractEntity: $transactional);

                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/transactional/list');
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/mailing/transactional/view',
                    params: [
                        'id' => $transactional->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /** @var Entity\Transactional $transactional */
                $transactional = $form->getData();
                $this->mailingService->save(entity: $transactional);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-transactional-%s-has-been-updated-successfully'),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/mailing/transactional/view',
                    params: [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form, 'transactional' => $transactional]);
    }
}
