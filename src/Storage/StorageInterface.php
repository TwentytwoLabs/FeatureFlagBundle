<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Storage;

use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

interface StorageInterface
{
    public function all(): array;

    public function get(string $feature): ?FeatureInterface;
}
