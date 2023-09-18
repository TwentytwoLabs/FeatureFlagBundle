<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Attribute;

#[\Attribute(flags: \Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Feature
{
    private string $name;
    private bool $enabled;

    public function __construct(string $name, bool $enabled = true)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function toArray(): array
    {
        return [
            'feature' => $this->name,
            'enabled' => $this->enabled,
        ];
    }
}
