<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Storage;

use TwentytwoLabs\FeatureFlagBundle\Model\Feature;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

class ArrayStorage implements StorageInterface
{
    private array $features;

    public function __construct(array $options = [])
    {
        $this->features = array_map(function (array $feature) {
            return new Feature($feature['name'], $feature['enabled'], $feature['description'] ?? null);
        }, $options['features']);
    }

    public function all(): array
    {
        return $this->features;
    }

    public function get(string $feature): ?FeatureInterface
    {
        return $this->features[$feature] ?? null;
    }
}
