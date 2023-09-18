<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('twentytwo_labs_feature_flag');
        $rootNode = $treeBuilder->getRootNode();

        /* @phpstan-ignore-next-line */
        $rootNode
            ->children()
                ->scalarNode('default_manager')->defaultValue('default')->end()
                ->arrayNode('managers')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('factory')->isRequired()->end()
                            ->variableNode('options')->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
