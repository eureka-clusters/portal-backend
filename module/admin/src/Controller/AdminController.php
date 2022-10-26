<?php

declare(strict_types=1);

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

final class AdminController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        return new ViewModel();
    }
}
