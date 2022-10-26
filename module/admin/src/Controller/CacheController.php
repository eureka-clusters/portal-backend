<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Form\AdminFunctions;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function array_map;
use function file_exists;
use function glob;
use function set_time_limit;
use function unlink;

/**
 * @method FlashMessenger flashMessenger()
 */
final class CacheController extends AbstractActionController
{
    public function __construct(
        private readonly Redis $cache,
    ) {
    }

    public function indexAction(): Response|ViewModel
    {
        $form = new AdminFunctions();
        $form->setData(data: $this->params()->fromPost());

        set_time_limit(seconds: 0);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            if (null !== $form->getData()[AdminFunctions::ACTION_CLEANUP_FILE_CACHE]) {
                /*
                 * To be deleted folders
                 */
                $rootPath = __DIR__ . '/../../../../';

                $cacheFolder = $rootPath . "data/twig/";
                if (file_exists(filename: $cacheFolder)) {
                    foreach (
                        new RecursiveIteratorIterator(
                            iterator: new RecursiveDirectoryIterator(directory: $cacheFolder),
                            mode: RecursiveIteratorIterator::LEAVES_ONLY
                        ) as $file
                    ) {
                        if ($file->isFile()) {
                            unlink(filename: $file->getPathname());
                        }
                    }
                }

                array_map(callback: 'unlink', array: glob(pattern: $rootPath . "data/cache/*"));

                $this->flashMessenger()->addInfoMessage(message: "Flush of File-cache successful");

                return $this->redirect()->toRoute(route: 'zfcadmin/cache/index');
            }

            if ((null !== $form->getData()[AdminFunctions::ACTION_FLUSH_REDIS_CACHE]) && $this->cache->flush()) {
                $this->flashMessenger()->addInfoMessage(message: "Flush of Redis cache successful");

                return $this->redirect()->toRoute(route: 'zfcadmin/cache/index');
            }
        }

        return new ViewModel(
            variables: [
                'form' => $form,
                'host' => $this->getRequest()->getServer()->get('SERVER_NAME'),
            ]
        );
    }
}
