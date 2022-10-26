<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Application\Service\FormService;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Exception;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Mailing\Entity;
use Mailing\Entity\Mailer;
use Mailing\Form\MailerFilter;
use Mailing\Service\EmailService;
use Mailing\Service\MailerService;

use function ceil;
use function sprintf;

use const PHP_INT_MAX;

final class MailerController extends MailingAbstractController
{
    public function __construct(
        private readonly MailerService $mailerService,
        private readonly EmailService $emailService,
        private readonly FormService $formService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getFilter();
        $userQuery = $this->mailerService
            ->findFiltered(Mailer::class, $filterPlugin->getFilter());

        $paginator = new Paginator(new PaginatorAdapter(new ORMPaginator($userQuery, false)));
        $paginator::setDefaultItemCountPerPage($page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber((int)$page);
        $paginator->setPageRange((int)ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new MailerFilter();

        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            [
                'paginator' => $paginator,
                'form' => $form,
                'order' => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
                'services' => Mailer::getServicesArray(),
            ]
        );
    }

    public function viewAction(): ViewModel|Response
    {
        $mailer = $this->mailerService->findMailerById((int)$this->params('id'));

        if (null === $mailer) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            try {
                $testEmail = $this->emailService->createNewCustomEmailBuilder($mailer);
                $testEmail->addUserTo($this->identity());
                $testEmail->setBody('This is a test');
                $testEmail->setSubject('This is a test subject');

                if ($this->emailService->send($testEmail)) {
                    $this->flashMessenger()->addSuccessMessage(
                        $this->translator->translate('txt-test-mail-sent-successfully')
                    );
                } else {
                    $this->flashMessenger()->addErrorMessage($this->translator->translate('txt-test-mail-failed'));
                }
            } catch (Exception $e) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf($this->translator->translate('txt-test-mail-failed-reason-%s'), $e->getMessage())
                );
            }

            return $this->redirect()
                ->toRoute('zfcadmin/mailing/mailer/view', ['id' => $mailer->getId()]);
        }

        return new ViewModel(
            [
                'mailer' => $mailer,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Mailer $mailer */
        $mailer = $this->mailerService->findMailerById((int)$this->params('id'));
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($mailer, $data);

        if (!$this->mailerService->canDeleteMailer($mailer)) {
            $form->remove('delete');
        }

        foreach ($this->mailerService->getRequiredFormFieldsByService($mailer->getService()) as $requiredFormField) {
            $form->getInputFilter()->get('mailing_entity_mailer')->get($requiredFormField)->setRequired(true);
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/mailer/view', ['id' => $mailer->getId()]);
            }

            if (isset($data['delete']) && $this->mailerService->canDeleteMailer($mailer)) {
                $this->mailerService->delete($mailer);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-mailer-%s-has-successfully-been-deleted'),
                        $mailer->getName()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/mailing/mailer/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Mailer $mailer */
                $mailer = $form->getData();
                $this->mailerService->save($mailer);

                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/mailer/view', ['id' => $mailer->getId()]);
            }
        }

        return new ViewModel(
            [
                'form' => $form,
                'mailer' => $mailer,
                'serviceName' => $mailer->getServiceText(),
                'formFields' => $this->mailerService->getFormFieldsByService($mailer->getService())
            ]
        );
    }

    public function newAction(): Response|ViewModel
    {
        $service = (int)$this->params('service');

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(Mailer::class, $data);
        $form->remove('delete');

        foreach ($this->mailerService->getRequiredFormFieldsByService($service) as $requiredFormField) {
            $form->getInputFilter()->get('mailing_entity_mailer')->get($requiredFormField)->setRequired(true);
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/mailing/mailer/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Mailer $mailer */
                $mailer = $form->getData();
                $mailer->setService($service);
                $this->mailerService->save($mailer);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-mailer-%s-has-successfully-been-created'),
                        $mailer->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/mailer/view', ['id' => $mailer->getId()]);
            }
        }

        return new ViewModel(
            [
                'form' => $form,
                'service' => $service,
                'serviceName' => Mailer::getServicesArray()[$service] ?? '',
                'formFields' => $this->mailerService->getFormFieldsByService($service)
            ]
        );
    }
}
