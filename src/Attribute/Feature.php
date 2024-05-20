<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Attribute;

abstract class Feature
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'feature' => $this->name,
            'enabled' => $this->shouldBeEnabled(),
        ];
    }

    abstract protected function shouldBeEnabled(): bool;
}
