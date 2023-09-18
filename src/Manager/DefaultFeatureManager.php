<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Manager;

use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

class DefaultFeatureManager implements FeatureManagerInterface
{
    private string $name;
    private StorageInterface $storage;

    public function __construct(string $name, StorageInterface $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function all(): iterable
    {
        return $this->storage->all();
    }

    public function isEnabled(string $feature): bool
    {
        return $this->storage->get($feature)?->isEnabled() ?? false;
    }

    public function isDisabled(string $feature): bool
    {
        return false === $this->isEnabled($feature);
    }
}
