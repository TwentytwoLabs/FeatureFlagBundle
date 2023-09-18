<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Manager;

use PHPUnit\Framework\TestCase;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use TwentytwoLabs\FeatureFlagBundle\Manager\DefaultFeatureManager;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class ChainedFeatureManagerTest extends TestCase
{
    private ChainedFeatureManager $manager;
    private DefaultFeatureManager $managerFoo;
    private DefaultFeatureManager $managerBar;

    protected function setUp(): void
    {
        $this->managerFoo = $this->createMock(DefaultFeatureManager::class);
        $this->managerBar = $this->createMock(DefaultFeatureManager::class);

        $this->manager = new ChainedFeatureManager(new \ArrayObject([$this->managerFoo, $this->managerBar]));
    }

    public function testAllFeaturesCanBeRetrievedFromAttachedStorage(): void
    {
        $this->assertEquals([$this->managerFoo, $this->managerBar], $this->manager->getManagers());
    }

    public function testIsFeatureEnabled(): void
    {
        $this->managerFoo->expects($this->once())->method('isEnabled')->with('feature_1')->willReturn(false);
        $this->managerBar->expects($this->once())->method('isEnabled')->with('feature_1')->willReturn(true);

        $this->assertTrue($this->manager->isEnabled('feature_1'));
    }

    public function testIsFeatureDisabled(): void
    {
        $this->managerFoo->expects($this->once())->method('isEnabled')->with('feature_1')->willReturn(false);
        $this->managerBar->expects($this->once())->method('isEnabled')->with('feature_1')->willReturn(false);

        $this->assertTrue($this->manager->isDisabled('feature_1'));
    }
}
