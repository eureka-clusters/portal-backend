<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Entity;use Application\Controller\Plugin\GetFilter;use Application\Controller\Plugin\Preferences;use Laminas\Mvc\Controller\AbstractActionController;use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;use Laminas\Mvc\Plugin\Identity\Identity;use Laminas\View\Model\ViewModel;

final class AdminController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        return new ViewModel();
    }
}
