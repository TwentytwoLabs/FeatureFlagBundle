<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TwentytwoLabs\FeatureFlagBundle\Checker\ExpressionLanguageChecker;
use TwentytwoLabs\FeatureFlagBundle\EventListener\ControllerListener;
use TwentytwoLabs\FeatureFlagBundle\EventListener\FeatureListener;
use TwentytwoLabs\FeatureFlagBundle\Factory\ArrayStorageFactory;
use TwentytwoLabs\FeatureFlagBundle\Factory\OrmStorageFactory;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use TwentytwoLabs\FeatureFlagBundle\Manager\FeatureManagerInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('twenty-two-labs.feature-flags.factory.array', ArrayStorageFactory::class);
    $services->set('twenty-two-labs.feature-flags.factory.orm', OrmStorageFactory::class)
        ->args([service('doctrine.orm.default_entity_manager')->nullOnInvalid()]);

    $services->set('twenty-two-labs.feature-flags.checker.expression_language', ExpressionLanguageChecker::class)
        ->args([
            service('security.expression_language')->nullOnInvalid(),
            service('security.authentication.trust_resolver')->nullOnInvalid(),
            service('security.role_hierarchy')->nullOnInvalid(),
            service('security.token_storage')->nullOnInvalid(),
            service('security.authorization_checker')->nullOnInvalid(),
            service('logger')->nullOnInvalid(),
        ])
    ;

    $services->instanceof(FeatureManagerInterface::class)
        ->tag('twenty-two-labs.feature-flags.manager');

    $services->set(ChainedFeatureManager::class)
        ->args([tagged_iterator('twenty-two-labs.feature-flags.manager')]);
    $services->alias('twenty-two-labs.feature-flags.manager', ChainedFeatureManager::class);

    $services->set(ControllerListener::class)
        ->tag('kernel.event_subscriber');

    $services->set(FeatureListener::class)
        ->args([service(ChainedFeatureManager::class)])
        ->tag('kernel.event_subscriber');
};
