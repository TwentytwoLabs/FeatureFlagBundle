<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Attribute;

#[\Attribute(flags: \Attribute::TARGET_ALL)]
final class IsFeatureDisabled extends Feature
{
    protected function shouldBeEnabled(): bool
    {
        return false;
    }
}
