<?php

declare(strict_types=1);

namespace Reporting\View\Helper;

use Application\ValueObject\Link\Link;
use Application\View\Helper\AbstractLink;
use Reporting\Entity\StorageLocation;

final class StorageLocationLink extends AbstractLink
{
    public function __invoke(
        ?\Reporting\Entity\StorageLocation $storageLocation = null,
        string $action = 'view',
        string $show = 'text'
    ): string {
        $linkParams      = [];
        $storageLocation ??= new StorageLocation();

        $routeParams = [];
        $showOptions = [];

        if (!$storageLocation->isEmpty()) {
            $routeParams['id']   = $storageLocation->getId();
            $showOptions['name'] = $storageLocation->getName();
        }

        switch ($action) {
            case 'new':
                $linkParams = [
                    'icon'  => 'fa-plus',
                    'route' => 'zfcadmin/reporting/storage-location/new',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(message: 'txt-new-storage-location'),
                ];
                break;
            case 'view':
                $linkParams = [
                    'icon'  => 'fa-link',
                    'route' => 'zfcadmin/reporting/storage-location/view',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(
                            message: 'txt-view-storage-location'
                        ),
                ];
                break;
            case 'edit':
                $linkParams = [
                    'icon'  => 'fa-pencil-square-o',
                    'route' => 'zfcadmin/reporting/storage-location/edit',
                    'text'  => $showOptions[$show] ?? $this->translator->translate(
                            message: 'txt-edit-storage-location'
                        ),
                ];
                break;
        }

        $linkParams['action']      = $action;
        $linkParams['show']        = $show;
        $linkParams['routeParams'] = $routeParams;

        return $this->parse(link: Link::fromArray(params: $linkParams));
    }
}
