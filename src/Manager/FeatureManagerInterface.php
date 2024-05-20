<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Manager;

use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

interface FeatureManagerInterface
{
    public function getName(): string;

    /**
     * @return iterable<FeatureInterface>
     */
    public function all(): iterable;

    public function isEnabled(string $key): bool;

    public function isDisabled(string $key): bool;
}
