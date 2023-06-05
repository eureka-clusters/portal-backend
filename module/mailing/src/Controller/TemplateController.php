<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Application\Service\FormService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Laminas\Paginator\Paginator;
use Laminas\View\Model\ViewModel;
use Mailing\Entity\Template;
use Mailing\Service\MailingService;

use function array_merge;
use function ceil;
use function sprintf;

use const PHP_INT_MAX;

final class TemplateController extends MailingAbstractController
{
    public function __construct(private readonly MailingService $mailingService, private readonly FormService $formService, private readonly TranslatorInterface $translator)
    {
    }

    public function listAction(): ViewModel
    {
        $templates = $this->mailingService->findAll(entity: Template::class);
        $page      = $this->getRequest()->getQuery()->get(name: 'page');

        $paginator = new Paginator(adapter: new ArrayAdapter(array: $templates));
        $paginator::setDefaultItemCountPerPage(count: $page === 'all' ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber(pageNumber: (int) $page);
        $paginator->setPageRange(pageRange: (int) ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $template = $this->mailingService->find(entity: Template::class, id: (int) $this->params('id'));

        return new ViewModel(
            variables: [
                'template' => $template,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Template $template */
        $template = $this->mailingService->find(entity: Template::class, id: (int) $this->params('id'));
        $data     = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        $form     = $this->formService->prepare(classNameOrEntity: $template, data: $data);

        if (! $this->mailingService->canDeleteTemplate(template: $template)) {
            $form->remove(elementOrFieldset: 'delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/template/view', params: ['id' => $template->getId()]);
            }

            if (isset($data['delete']) && $this->mailingService->canDeleteTemplate(template: $template)) {
                $this->mailingService->delete(entity: $template);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: "txt-template-%s-has-successfully-been-deleted"),
                        $template->getName()
                    )
                );

                return $this->redirect()->toRoute(route: 'zfcadmin/mailing/template/list');
            }

            if ($form->isValid()) {
                /** @var Template $template */
                $template = $form->getData();
                $this->mailingService->save(entity: $template);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: "txt-template-%s-has-successfully-been-updated"),
                        $template->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/template/view', params: ['id' => $template->getId()]);
            }
        }

        return new ViewModel(variables: ['form' => $form, 'template' => $template]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: Template::class, data: $data);

        $form->remove(elementOrFieldset: 'delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/template/list');
            }

            if ($form->isValid()) {
                /** @var Template $template */
                $template = $form->getData();
                $this->mailingService->save(entity: $template);

                $this->flashMessenger()->addSuccessMessage(
                    message: sprintf(
                        $this->translator->translate(message: "txt-template-%s-has-successfully-been-created"),
                        $template->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute(route: 'zfcadmin/mailing/template/view', params: ['id' => $template->getId()]);
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }
}
