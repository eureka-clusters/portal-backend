<?php

declare(strict_types=1);

namespace Mailing\Controller;

use Admin\Entity\User;
use Application\Controller\Plugin\GetFilter;
use BjyAuthorize\Controller\Plugin\IsAllowed;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * @method User identity()
 * @method FlashMessenger flashMessenger()
 * @method IsAllowed isAllowed($resource, $action)
 * @method GetFilter getFilter()
 */
abstract class MailingAbstractController extends AbstractActionController
{
    protected static array $defaultReturn
        = [
            'success' => false,
            'errors'  => [],
            'data'    => [],
        ];
}
