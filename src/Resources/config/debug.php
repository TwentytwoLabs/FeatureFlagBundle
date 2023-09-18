<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TwentytwoLabs\FeatureFlagBundle\DataCollector\FeatureCollector;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set(FeatureCollector::class)
        ->autowire()
        ->tag(
            'data_collector',
            [
                'template' => '@TwentytwoLabsFeatureFlag/data_collector/template.html.twig',
                'id' => 'twenty-two-labs.feature-flags.collector',
            ]
        );
};
