<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Manager;

interface FeatureManagerInterface
{
    public function getName(): string;

    public function all(): iterable;

    public function isEnabled(string $feature): bool;

    public function isDisabled(string $feature): bool;
}
