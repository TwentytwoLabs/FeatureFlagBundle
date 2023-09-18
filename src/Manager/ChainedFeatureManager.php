<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Manager;

class ChainedFeatureManager
{
    private array $featureManagers;

    public function __construct(\Traversable $featureManagers)
    {
        $this->featureManagers = iterator_to_array($featureManagers);
    }

    public function getManagers(): iterable
    {
        return $this->featureManagers;
    }

    public function isEnabled(string $feature): bool
    {
        foreach ($this->featureManagers as $featureManager) {
            if ($featureManager->isEnabled($feature)) {
                return true;
            }
        }

        return false;
    }

    public function isDisabled(string $feature): bool
    {
        return false === $this->isEnabled($feature);
    }
}
