<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Attribute;

use TwentytwoLabs\FeatureFlagBundle\Attribute\IsFeatureEnabled;
use PHPUnit\Framework\TestCase;

final class IsFeatureEnabledTest extends TestCase
{
    public function testShouldValidateToArrayResult(): void
    {
        $feature = new IsFeatureEnabled('foo');

        $this->assertSame('foo', $feature->getName());
        $this->assertSame(['feature' => 'foo', 'enabled' => true], $feature->toArray());
    }
}
