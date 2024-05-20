<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Reference;
use TwentytwoLabs\FeatureFlagBundle\Command\ListFeatureCommand;
use TwentytwoLabs\FeatureFlagBundle\DataCollector\FeatureCollector;
use TwentytwoLabs\FeatureFlagBundle\DependencyInjection\TwentytwoLabsFeatureFlagExtension;
use TwentytwoLabs\FeatureFlagBundle\Twig\Extension\FeatureFlagExtension;

final class TwentytwoLabsFeatureFlagExtensionTest extends AbstractExtensionTestCase
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

    public function testShouldLoadWithDefaultConfig(): void
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

    public function testShouldLoadWithFullConfig(): void
    {
        $expectedConfig = [
            'default_manager' => 'manager_foo',
            'managers' => [
                'manager_foo' => [
                    'factory' => 'twenty-two-labs.feature-flags.factory.array',
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
                    'factory' => 'twenty-two-labs.feature-flags.factory.array',
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

        $storageFoo = $this->container->findDefinition('twenty-two-labs.feature-flags.storage.manager_foo');
        $this->assertFalse($storageFoo->isPublic());
        $factory = $storageFoo->getFactory();
        $this->assertIsArray($factory);
        $this->assertCount(2, $factory);
        $this->assertArrayHasKey(0, $factory);
        $this->assertInstanceOf(Reference::class, $factory[0]);
        $this->assertSame('twenty-two-labs.feature-flags.factory.array', (string) $factory[0]);
        $this->assertArrayHasKey(1, $factory);
        $this->assertSame('createStorage', $factory[1]);

        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.manager.manager_bar');
        $this->assertContainerBuilderHasService('twenty-two-labs.feature-flags.storage.manager_bar');

        $storageBar = $this->container->findDefinition('twenty-two-labs.feature-flags.storage.manager_bar');
        $this->assertFalse($storageBar->isPublic());
        $factory = $storageBar->getFactory();
        $this->assertIsArray($factory);
        $this->assertCount(2, $factory);
        $this->assertArrayHasKey(0, $factory);
        $this->assertInstanceOf(Reference::class, $factory[0]);
        $this->assertSame('twenty-two-labs.feature-flags.factory.array', (string) $factory[0]);
        $this->assertArrayHasKey(1, $factory);
        $this->assertSame('createStorage', $factory[1]);
    }
}
