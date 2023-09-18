<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Model;

interface FeatureInterface
{
    public function getKey(): string;

    public function isEnabled(): bool;

    public function getDescription(): ?string;

    public function toArray(): array;
}
