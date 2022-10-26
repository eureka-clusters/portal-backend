<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Application\Entity\AbstractEntity;
use Application\Options\ModuleOptions;
use Application\ValueObject\Link\Link;
use BjyAuthorize\Service\Authorize;
use Jield\Authorize\Service\AssertionService;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\RouteStackInterface;

abstract class AbstractLink
{
    public function __construct(
        private readonly AssertionService $assertionService,
        private readonly Authorize $authorizeService,
        private readonly RouteStackInterface $router,
        protected TranslatorInterface $translator,
        private readonly ModuleOptions $moduleOptions
    ) {
    }

    protected function parse(?Link $link): string
    {
        return $link === null ? '' : $link->parse($this->router, $this->moduleOptions->getServerUrl());
    }

    protected function hasAccess(AbstractEntity $entity, string $assertionName, string $action): bool
    {
        $this->assertionService->addResource($entity, $assertionName);
        return $this->authorizeService->isAllowed($entity, $action);
    }
}
