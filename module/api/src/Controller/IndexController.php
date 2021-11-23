<?php

declare(strict_types=1);

namespace Api\Controller;

use Laminas\Mvc\Controller\AbstractActionController;

final class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return $this->redirect()->toRoute('api-tools/ui');
    }
}
