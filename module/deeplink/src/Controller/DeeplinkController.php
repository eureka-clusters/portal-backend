<?php

declare(strict_types=1);

namespace Deeplink\Controller;

use DateTime;
use Deeplink\Service\DeeplinkService;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * final class DeeplinkController
 */
final class DeeplinkController extends AbstractActionController
{
    public function __construct(
        private readonly DeeplinkService $deeplinkService,
        private readonly AuthenticationService $authenticationService,
    ) {
    }

    public function deeplinkAction(): Response|ViewModel
    {
        $deeplink = $this->deeplinkService->findDeeplinkByHash(hash: (string) $this->params('hash'));

        if (null === $deeplink) {
            return $this->notFoundAction();
        }
        if ($deeplink->getEndDate() < new DateTime()) {
            return $this->notFoundAction();
        }

        $user = $deeplink->getUser();
        $this->authenticationService->getStorage()->write(contents: $user);

        $deeplink->setDateAccess(dateAccess: new DateTime());
        $this->deeplinkService->save(entity: $deeplink);

        return $this->redirect()->toRoute(
            route: $deeplink->getTarget()->getRoute(),
            params: [
                'id'     => $deeplink->getKeyId(),
                'docRef' => $deeplink->getKeyId(),
            ]
        );
    }
}
