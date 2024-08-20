<?php

declare(strict_types=1);

namespace Application\Controller;

use Admin\Service\oAuth2Service;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class IndexController extends AbstractActionController
{
    public function __construct(
        private readonly oAuth2Service $oAuth2Service,
    ) {
    }

    #[\Override]
    public function indexAction(): ViewModel
    {
        return new ViewModel(
            ['services' => $this->oAuth2Service->findAllService()],
        );
    }
}
