<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Manager;

use TwentytwoLabs\FeatureFlagBundle\Manager\DefaultFeatureManager;
use PHPUnit\Framework\TestCase;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class DefaultFeatureManagerTest extends TestCase
{
    private StorageInterface $storage;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(StorageInterface::class);
    }

    public function testShouldValidateName(): void
    {
        $manager = $this->getManager();
        $this->assertSame('foo', $manager->getName());
    }

    public function testShouldRecoverAllFunctionality(): void
    {
        $this->storage->expects($this->once())->method('all')->willReturn([]);

        $manager = $this->getManager();
        $this->assertSame([], $manager->all());
    }

    public function testShouldRetrieveStatusOfFeatureWhenFeatureNotExist(): void
    {
        $this->storage->expects($this->exactly(2))->method('get')->with('feature_1')->willReturn(null);

        $manager = $this->getManager();
        $this->assertFalse($manager->isEnabled('feature_1'));
        $this->assertTrue($manager->isDisabled('feature_1'));
    }

    public function testShouldRetrieveStatusOfFeatureWhenFeatureIsEnable(): void
    {
        $feature = $this->createMock(FeatureInterface::class);
        $feature->expects($this->exactly(2))->method('isEnabled')->willReturn(true);

        $this->storage->expects($this->exactly(2))->method('get')->with('feature_1')->willReturn($feature);

        $manager = $this->getManager();
        $this->assertTrue($manager->isEnabled('feature_1'));
        $this->assertFalse($manager->isDisabled('feature_1'));
    }

    public function testShouldRetrieveStatusOfFeatureWhenFeatureIsDisable(): void
    {
        $feature = $this->createMock(FeatureInterface::class);
        $feature->expects($this->exactly(2))->method('isEnabled')->willReturn(true);

        $this->storage->expects($this->exactly(2))->method('get')->with('feature_1')->willReturn($feature);

        $manager = $this->getManager();
        $this->assertTrue($manager->isEnabled('feature_1'));
        $this->assertFalse($manager->isDisabled('feature_1'));
    }

    private function getManager(): DefaultFeatureManager
    {
        return new DefaultFeatureManager('foo', $this->storage);
    }
}
