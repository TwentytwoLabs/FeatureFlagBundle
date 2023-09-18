<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use TwentytwoLabs\FeatureFlagBundle\DependencyInjection\Configuration;
use TwentytwoLabs\FeatureFlagBundle\DependencyInjection\TwentytwoLabsFeatureFlagExtension;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testShouldCheckEmptyConfiguration(): void
    {
        $expectedEmptyConfig = [
            'default_manager' => 'default',
            'managers' => [],
        ];

        $this->assertProcessedConfigurationEquals(
            $expectedEmptyConfig,
            [sprintf('%s/../Fixtures/Resources/config/empty.yaml', __DIR__)]
        );
    }
    public function testShouldCheckConfiguration(): void
    {
        $expectedConfig = [
            'default_manager' => 'manager_foo',
            'managers' => [
                'manager_foo' => [
                    'factory' => 'novaway_feature_flag.factory.array',
                    'options' => [
                        'features' => [
                            'my_feature_1' => null,
                            'my_feature_2' => true,
                            'my_feature_3' => ['enabled' => false],
                            'my_feature_4' => ['enabled' => true, 'description' => 'MyFeature4 description text'],
                        ],
                    ],
                ],
                'manager_bar' => [
                    'factory' => 'novaway_feature_flag.factory.array',
                    'options' => [
                        'features' => [
                            'my_feature_5' => false,
                            'my_feature_6' => [],
                            'my_feature_7' => false,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals(
            $expectedConfig,
            [sprintf('%s/../Fixtures/Resources/config/full.yaml', __DIR__)]
        );
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new TwentytwoLabsFeatureFlagExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
