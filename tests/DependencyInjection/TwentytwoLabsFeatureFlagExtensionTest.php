<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use TwentytwoLabs\FeatureFlagBundle\Command\ListFeatureCommand;
use TwentytwoLabs\FeatureFlagBundle\DataCollector\FeatureCollector;
use TwentytwoLabs\FeatureFlagBundle\Twig\Extension\FeatureFlagExtension;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class TwentytwoLabsFeatureFlagExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setParameter('kernel.debug', true);
    }

    protected function getContainerExtensions(): array
    {
        return [new TwentytwoLabsFeatureFlagExtension()];
    }

    public function testShouldLoadWithDefaultConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasService(FeatureCollector::class);
        $this->assertContainerBuilderHasService(FeatureFlagExtension::class);
        $this->assertContainerBuilderHasService(ListFeatureCommand::class);
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.factory.array');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.manager');
        $this->assertContainerBuilderNotHasService('twenty-two-labs.feature-flags.manager.manager_foo');
        $this->assertContainerBuilderNotHasService('twenty-two-labs.feature-flags.storage.manager_foo');
        $this->assertContainerBuilderNotHasService('twenty-two-labs.feature-flags.manager.manager_bar');
        $this->assertContainerBuilderNotHasService('twenty-two-labs.feature-flags.storage.manager_bar');
    }

    public function testShouldLoadWithFullConfig()
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
        $this->load($expectedConfig);

        $this->assertContainerBuilderHasService(FeatureCollector::class);
        $this->assertContainerBuilderHasService(FeatureFlagExtension::class);
        $this->assertContainerBuilderHasService(ListFeatureCommand::class);
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.factory.array');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.manager');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.manager.manager_foo');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.storage.manager_foo');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.manager.manager_bar');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.storage.manager_bar');
    }
}
