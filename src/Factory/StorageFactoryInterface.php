<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Factory;

use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

interface StorageFactoryInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function createStorage(string $storageName, array $options = []): StorageInterface;
}
