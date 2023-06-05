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
        $page         = $this->params()->fromRoute(param: 'page', default: 1);
        $filterPlugin = $this->getFilter();
        $userQuery    = $this->mailerService
            ->findFiltered(entity: Mailer::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(
                paginator: new ORMPaginator(
                    query: $userQuery,
                    fetchJoinCollection: false
                )
            )
        );
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 20);
        $paginator->setCurrentPageNumber(pageNumber: (int) $page);
        $paginator->setPageRange(pageRange: (int) ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new MailerFilter();

        $form->setData(data: $filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
                'form'      => $form,
                'order'     => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
                'services'  => Mailer::getServicesArray(),
            ]
        );
    }

    public function viewAction(): ViewModel|Response
    {
        $mailer = $this->mailerService->findMailerById(id: (int) $this->params('id'));

        if (null === $mailer) {
            return $this->notFoundAction();
        }

        if ($this->getRequest()->isPost()) {
            try {
                $testEmail = $this->emailService->createNewCustomEmailBuilder(mailer: $mailer);
                $testEmail->addUserTo(user: $this->identity());
                $testEmail->setBody(body: 'This is a test');
                $testEmail->setSubject(subject: 'This is a test subject');

                if ($this->emailService->send(emailBuilder: $testEmail)) {
                    $this->flashMessenger()->addSuccessMessage(
                        message: $this->translator->translate(message: 'txt-test-mail-sent-successfully')
                    );
                } else {
                    $this->flashMessenger()->addErrorMessage(
                        message: $this->translator->translate(
                            message: 'txt-test-mail-failed'
                        )
                    );
                }
            } catch (Exception $e) {
                $this->flashMessenger()->addErrorMessage(
                    message: sprintf($this->translator->translate(message: 'txt-test-mail-failed-reason-%s'), $e->getMessage())
                );
            }

            return $this->redirect()
                ->toRoute(route: 'zfcadmin/mailing/mailer/view', params: ['id' => $mailer->getId()]);
        }

        return new ViewModel(
            variables: [
                'mailer' => $mailer,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Mailer $mailer */
        $mailer = $this->mailerService->findMailerById(id: (int) $this->params('id'));
        $data   = $this->getRequest()->getPost()->toArray();
        $form   = $this->formService->prepare(classNameOrEntity: $mailer, data: $data);

        if (! $this->mailerService->canDeleteMailer(mailer: $mailer)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        foreach (
            $this->mailerService->getRequiredFormFieldsByService(
                service: $mailer->getService()
            ) as $requiredFormField
        ) {
            $form->getInputFilter()->get(name: 'mailing_entity_mailer')->get(name: $requiredFormField)->setRequired(
                required: true
            );
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/mailer/view', params: ['id' => $mailer->getId()]);
            }

            if (isset($data['delete']) && $this->mailerService->canDeleteMailer(mailer: $mailer)) {
                $this->mailerService->delete(entity: $mailer);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-mailer-%s-has-successfully-been-deleted'),
                        $mailer->getName()
                    )
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/mailer/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Mailer $mailer */
                $mailer = $form->getData();
                $this->mailerService->save(entity: $mailer);

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/mailer/view', params: ['id' => $mailer->getId()]);
            }
        }

        return new ViewModel(
            variables: [
                'form'        => $form,
                'mailer'      => $mailer,
                'serviceName' => $mailer->getServiceText(),
                'formFields'  => $this->mailerService->getFormFieldsByService(service: $mailer->getService()),
            ]
        );
    }

    public function newAction(): Response|ViewModel
    {
        $service = (int) $this->params('service');

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: Mailer::class, data: $data);
        $form->remove(elementOrFieldset: 'delete');

        foreach ($this->mailerService->getRequiredFormFieldsByService(service: $service) as $requiredFormField) {
            $form->getInputFilter()->get(name: 'mailing_entity_mailer')->get(name: $requiredFormField)->setRequired(
                required: true
            );
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/mailer/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Mailer $mailer */
                $mailer = $form->getData();
                $mailer->setService(service: $service);
                $this->mailerService->save(entity: $mailer);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: 'txt-mailer-%s-has-successfully-been-created'),
                        $mailer->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/mailer/view', params: ['id' => $mailer->getId()]);
            }
        }

        return new ViewModel(
            variables: [
                'form'        => $form,
                'service'     => $service,
                'serviceName' => Mailer::getServicesArray()[$service] ?? '',
                'formFields'  => $this->mailerService->getFormFieldsByService(service: $service),
            ]
        );
    }
}
