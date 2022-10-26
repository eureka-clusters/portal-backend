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
        $templates = $this->mailingService->findAll(Template::class);
        $page      = $this->getRequest()->getQuery()->get('page');

        $paginator = new Paginator(new ArrayAdapter($templates));
        $paginator::setDefaultItemCountPerPage($page === 'all' ? PHP_INT_MAX : 25);
        $paginator->setCurrentPageNumber((int) $page);
        $paginator->setPageRange((int) ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        return new ViewModel(
            [
                'paginator' => $paginator,
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $template = $this->mailingService->find(Template::class, (int) $this->params('id'));

        return new ViewModel(
            [
                'template' => $template,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Template $template */
        $template = $this->mailingService->find(Template::class, (int) $this->params('id'));
        $data     = array_merge($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        $form     = $this->formService->prepare($template, $data);

        if (! $this->mailingService->canDeleteTemplate($template)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/template/view', ['id' => $template->getId()]);
            }

            if (isset($data['delete']) && $this->mailingService->canDeleteTemplate($template)) {
                $this->mailingService->delete($template);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-template-%s-has-successfully-been-deleted"),
                        $template->getName()
                    )
                );

                return $this->redirect()->toRoute('zfcadmin/mailing/template/list');
            }

            if ($form->isValid()) {
                /** @var Template $template */
                $template = $form->getData();
                $this->mailingService->save($template);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-template-%s-has-successfully-been-updated"),
                        $template->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/template/view', ['id' => $template->getId()]);
            }
        }

        return new ViewModel(['form' => $form, 'template' => $template]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(Template::class, $data);

        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/template/list');
            }

            if ($form->isValid()) {
                /** @var Template $template */
                $template = $form->getData();
                $this->mailingService->save($template);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate("txt-template-%s-has-successfully-been-created"),
                        $template->getName()
                    )
                );

                return $this->redirect()
                    ->toRoute('zfcadmin/mailing/template/view', ['id' => $template->getId()]);
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
