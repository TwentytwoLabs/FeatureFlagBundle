<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Attribute;

use TwentytwoLabs\FeatureFlagBundle\Attribute\Feature;
use PHPUnit\Framework\TestCase;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
final class FeatureTest extends TestCase
{
    public function testShouldValidateToArrayResult(): void
    {
        $feature = new Feature('foo', false);

        $this->assertSame('foo', $feature->getName());
        $this->assertFalse($feature->isEnabled());
        $this->assertSame(['feature' => 'foo', 'enabled' => false], $feature->toArray());
    }

    public function testShouldValidateToArrayResultWithInitialValues()
    {
        $feature = new Feature('bar');

        $this->assertSame('bar', $feature->getName());
        $this->assertTrue($feature->isEnabled());
        $this->assertSame(['feature' => 'bar', 'enabled' => true], $feature->toArray());
    }
}
