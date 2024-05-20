<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Model;

interface FeatureInterface
{
    public function getKey(): string;

    public function isEnabled(): bool;

    public function getExpression(): ?string;

    public function getDescription(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
