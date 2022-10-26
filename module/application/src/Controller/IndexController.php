<?php

declare(strict_types=1);

namespace Application\Controller;

use Admin\Service\OAuth2Service;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    public function __construct(
        private readonly OAuth2Service $oAuth2Service,
    ) {
    }

    public function indexAction(): ViewModel
    {
        return new ViewModel(
            ['services' => $this->oAuth2Service->findAllService()],
        );
    }
}
