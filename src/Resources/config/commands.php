<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TwentytwoLabs\FeatureFlagBundle\Command\ListFeatureCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(ListFeatureCommand::class)
        ->autowire()
        ->tag('console.command')
    ;
};
