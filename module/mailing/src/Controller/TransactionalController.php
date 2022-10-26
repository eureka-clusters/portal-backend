<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Admin\Entity\User;
use Application\Controller\Plugin\GetFilter;
use Application\Controller\Plugin\Preferences;
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
use function urlencode;

/**
 * @method GetFilter getFilter()
 * @method Preferences preferences()
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
            Transactional::class,
            $filterPlugin->getFilter()
        );

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($transactionalQuery, false)));
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

    public function viewAction(): Response|ViewModel
    {
        /** @var Entity\Transactional $transactional */
        $transactional = $this->mailingService->find(Transactional::class, (int)$this->params('id'));

        if (null === $transactional) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            $email = $this->emailService->createNewTransactionalEmailBuilder(transactionalOrKey: $transactional);
            $email->setSender(null, $this->identity());
            $email->addUserTo($this->identity());

            if ($this->emailService->send($email)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-transactional-email-with-name-%s-has-been-sent-successfully'),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/mailing/transactional/view',
                    [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['transactional' => $transactional]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare(Transactional::class, $data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/mailing/transactional/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Transactional $transactional */
                $transactional = $form->getData();
                $this->mailingService->save($transactional);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-transactional-%s-has-been-created-successfully'),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/mailing/transactional/view',
                    [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Transactional $transactional */
        $transactional = $this->mailingService->find(Transactional::class, (int)$this->params('id'));

        if (null === $transactional) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = $this->formService->prepare($transactional, $data);

        if (!$this->mailingService->canDeleteTransactional($transactional)) {
            $form->remove('delete');

            $form->get('mailing_entity_transactional')->get('key')->setAttribute('disabled', 'disabled');
            $form->getInputFilter()->get('mailing_entity_transactional')->get('key')->setRequired(false);
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete']) && $this->mailingService->canDeleteTransactional($transactional)) {
                $this->mailingService->delete($transactional);

                return $this->redirect()->toRoute('zfcadmin/mailing/transactional/list');
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/mailing/transactional/view',
                    [
                        'id' => $transactional->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /** @var Entity\Transactional $transactional */
                $transactional = $form->getData();
                $this->mailingService->save($transactional);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-transactional-%s-has-been-updated-successfully'),
                        $transactional->getName()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/mailing/transactional/view',
                    [
                        'id' => $transactional->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'transactional' => $transactional]);
    }
}
