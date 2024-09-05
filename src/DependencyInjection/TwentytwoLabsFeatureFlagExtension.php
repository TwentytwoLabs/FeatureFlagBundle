<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use TwentytwoLabs\FeatureFlagBundle\Storage\CachedStorage;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;
use TwentytwoLabs\FeatureFlagBundle\Manager\DefaultFeatureManager;

class TwentytwoLabsFeatureFlagExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(sprintf('%s/../Resources/config', __DIR__)));
        $loader->load('commands.php');
        $loader->load('services.php');
        $loader->load('twig.php');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.php');
        }

        foreach ($config['managers'] as $name => $managerConfiguration) {
            $container
                ->register(sprintf('twenty-two-labs.feature-flags.storage.%s', $name), StorageInterface::class)
                ->setFactory([new Reference($managerConfiguration['factory']), 'createStorage'])
                ->addArgument($name)
                ->addArgument($managerConfiguration['options'])
                ->setPublic(false)
            ;

            $storageFactoryId = sprintf('twenty-two-labs.feature-flags.storage.%s', $name);
            if (true === $config['cache']['enabled'] && !empty($config['cache']['provider'])) {
                $container
                    ->register(sprintf('twenty-two-labs.feature-flags.storage.cached_%s', $name), CachedStorage::class)
                    ->addArgument(new Reference($storageFactoryId))
                    ->addArgument(new Reference($config['cache']['provider']))
                    ->addArgument(['expiresAfter' => $config['cache']['expires_after']])
                ;
                $storageFactoryId = sprintf('twenty-two-labs.feature-flags.storage.cached_%s', $name);
            }

            $container
                ->register(sprintf('twenty-two-labs.feature-flags.manager.%s', $name), DefaultFeatureManager::class)
                ->addArgument($name)
                ->addArgument(new Reference($storageFactoryId))
                ->addArgument(new Reference('twenty-two-labs.feature-flags.checker.expression_language'))
                ->addTag('twenty-two-labs.feature-flags.manager')
            ;
        }
    }
}
