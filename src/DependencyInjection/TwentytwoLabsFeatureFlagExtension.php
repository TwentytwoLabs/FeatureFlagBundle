<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;
use TwentytwoLabs\FeatureFlagBundle\Manager\DefaultFeatureManager;

class TwentytwoLabsFeatureFlagExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
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

            $container
                ->register(sprintf('twenty-two-labs.feature-flags.manager.%s', $name), DefaultFeatureManager::class)
                ->addArgument($name)
                ->addArgument(new Reference(sprintf('twenty-two-labs.feature-flags.storage.%s', $name)))
                ->addArgument(new Reference('twenty-two-labs.feature-flags.checker.expression_language'))
                ->addTag('twenty-two-labs.feature-flags.manager')
            ;
        }
    }
}
