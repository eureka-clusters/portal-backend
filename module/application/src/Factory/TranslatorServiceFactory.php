<?php

declare(strict_types=1);

namespace Application\Factory;

use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Override;
use Psr\Container\ContainerInterface;

final class TranslatorServiceFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Translator
    {
        // Configure the translator
        $config           = $container->get('config');
        $translatorConfig = $config['translator'] ?? [];

        if (isset($translatorConfig['cache']) && $translatorConfig['cache'] !== false && $container->has(
                Redis::class
            )) {
            $translatorConfig['cache'] = $container->get(Redis::class);
        } else {
            unset($translatorConfig['cache']);
        }

        $translator = Translator::factory(options: $translatorConfig);

        if ($container->has('TranslatorPluginManager')) {
            $translator->setPluginManager($container->get('TranslatorPluginManager'));
        }

        return $translator;
    }
}
