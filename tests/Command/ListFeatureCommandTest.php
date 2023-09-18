<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TwentytwoLabs\FeatureFlagBundle\Command\ListFeatureCommand;
use PHPUnit\Framework\TestCase;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use TwentytwoLabs\FeatureFlagBundle\Manager\FeatureManagerInterface;
use TwentytwoLabs\FeatureFlagBundle\Model\Feature;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class ListFeatureCommandTest extends TestCase
{
    #[DataProvider('featuresProvider')]
    public function testConfiguredFeaturesAreDisplayedInAskedFormat(array $features, string $expectedOutput): void
    {
        $commandTester = $this->createCommandTester($features);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertSame($expectedOutput, trim($commandTester->getDisplay()));
    }

    public static function featuresProvider(): iterable
    {
        return [
            'empty-features' => [
                [
                    'foo' => [
                        'options' => [
                            'features' => [],
                        ]
                    ],
                ],
                "foo\n---\n\n [WARNING] No feature declared.",
            ],
            'with-features' => [
                [
                    'foo' => [
                        'options' => [
                            'features' => [
                                'feature1' => [
                                    'name' => 'feature1',
                                    'enabled' => true,
                                    'description' => 'Feature 1 description',
                                ],
                                'feature2' => [
                                    'name' => 'feature2',
                                    'enabled' => false,
                                    'description' => 'Feature 2 description',
                                ],
                            ],
                        ]
                    ],
                ],
                "foo\n---\n\n ---------- --------- ----------------------- \n  Name       Enabled   Description            \n ---------- --------- ----------------------- \n  feature1   Yes       Feature 1 description  \n  feature2   No        Feature 2 description  \n ---------- --------- -----------------------",
            ],
        ];
    }

    private function createCommandTester(array $managersDefinition = []): CommandTester
    {
        $managers = [];
        foreach ($managersDefinition as $managerName => $featuresDefinition) {
            $manager = $this->createMock(FeatureManagerInterface::class);
            $manager->expects($this->once())->method('getName')->willReturn($managerName);
            $manager->expects($this->once())->method('all')->willReturn(array_map(function (array $feature) {
                return new Feature(
                    key: $feature['name'],
                    enabled: $feature['enabled'],
                    expression: $feature['expression'] ?? null,
                    description: $feature['description'] ?? null
                );
            }, $featuresDefinition['options']['features']));

            $managers[] = $manager;
        }

        $chainedFeatureManager = $this->createMock(ChainedFeatureManager::class);
        $chainedFeatureManager->expects($this->once())->method('getManagers')->willReturn($managers);

        $command = new ListFeatureCommand($chainedFeatureManager);

        return new CommandTester($command);
    }
}
