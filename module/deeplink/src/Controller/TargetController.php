<?php

declare(strict_types=1);

namespace Deeplink\Controller;

use Application\Service\FormService;
use Deeplink\Entity;
use Deeplink\Entity\Target;
use Deeplink\Form\Manage;
use Deeplink\Service\DeeplinkService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\View\Model\ViewModel;

use function count;
use function is_array;
use function is_countable;
use function sprintf;

/**
 * final class TargetController
 *
 * @method FlashMessenger flashMessenger()
 */
final class TargetController extends AbstractActionController
{
    public function __construct(
        private readonly DeeplinkService $deeplinkService,
        private readonly FormService $formService,
        private readonly TreeRouteStack $router,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): Response|ViewModel
    {
        $targets = $this->deeplinkService->findTargets();

        $form = new Manage();
        $data = $this->getRequest()->getPost()->toArray();

        if ($this->getRequest()->isPost()) {
            if (isset($data['deleteInactiveDeeplinks'])) {
                $this->deeplinkService->deleteInactiveDeeplinks();

                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate('txt-all-expired-deeplinks-have-been-removed-successfully')
                );

                return $this->redirect()->toRoute('zfcadmin/deeplink/target/list');
            }

            if (isset($data['deleteTargets'])) {
                if (isset($data['target']) && is_array($data['target'])) {
                    foreach ($data['target'] as $targetId) {
                        /** @var Entity\Target $target */
                        $target = $this->deeplinkService->find(Target::class, (int)$targetId);
                        $this->deeplinkService->delete($target);
                    }

                    $this->flashMessenger()->addSuccessMessage(
                        sprintf(
                            $this->translator->translate('txt-%s-deeplink-targets-have-been-removed-successfully'),
                            is_countable($data['target']) ? count($data['target']) : 0
                        )
                    );
                }

                return $this->redirect()->toRoute('zfcadmin/deeplink/target/list');
            }
        }

        return new ViewModel(
            [
                'targets'         => $targets,
                'form'            => $form,
                'deeplinkService' => $this->deeplinkService,
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        /** @var Entity\Target $target */
        $target = $this->deeplinkService->find(Target::class, (int)$this->params('id'));

        if (null === $target) {
            return $this->notFoundAction();
        }

        return new ViewModel(
            [
                'target'          => $target,
                'router'          => $this->router,
                'deeplinkService' => $this->deeplinkService,
            ]
        );
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\Target $target */
        $target = $this->deeplinkService->find(Target::class, (int)$this->params('id'));

        if (null === $target) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare($target, $data);

        if (!$this->deeplinkService->targetCanBeDeleted($target)) {
            $form->remove('delete');
        }

        if ($this->getRequest()->isPost()) {
            if (isset($data['delete']) && $this->deeplinkService->targetCanBeDeleted($target)) {
                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-deeplink-target-%s-has-successfully-been-deleted'),
                        $target->getTarget()
                    )
                );

                $this->deeplinkService->delete($target);

                return $this->redirect()->toRoute('zfcadmin/deeplink/target/list');
            }

            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/deeplink/target/view',
                    [
                        'id' => $target->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                /** @var Entity\Target $target */
                $target = $form->getData();
                $this->deeplinkService->save($target);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-deeplink-target-%s-has-successfully-been-updated'),
                        $target->getTarget()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/deeplink/target/view',
                    [
                        'id' => $target->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'target' => $target]);
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(Target::class, $data);
        $form->remove('delete');

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('zfcadmin/deeplink/target/list');
            }

            if ($form->isValid()) {
                /** @var Entity\Target $target */
                $target = $form->getData();
                $this->deeplinkService->save($target);

                $this->flashMessenger()->addSuccessMessage(
                    sprintf(
                        $this->translator->translate('txt-deeplink-target-%s-has-successfully-been-created'),
                        $target->getTarget()
                    )
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/deeplink/target/view',
                    [
                        'id' => $target->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }
}
