<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;

class FeatureCollector extends DataCollector
{
    private ChainedFeatureManager $manager;

    public function __construct(ChainedFeatureManager $manager)
    {
        $this->manager = $manager;
    }

    public function getName(): string
    {
        return 'twenty-two-labs.feature-flags.collector';
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $totalFeatureCount = 0;
        $activeFeatureCount = 0;

        $features = [];
        foreach ($this->manager->getManagers() as $manager) {
            $features[$manager->getName()] = [];
            foreach ($manager->all() as $feature) {
                $features[$manager->getName()][] = $feature->toArray();

                if ($feature->isEnabled()) {
                    ++$activeFeatureCount;
                }

                ++$totalFeatureCount;
            }
        }

        $this->data = [
            'features' => $features,
            'totalFeatureCount' => $totalFeatureCount,
            'activeFeaturesCount' => $activeFeatureCount,
        ];
    }

    public function getFeatures(): array
    {
        return $this->data['features'];
    }

    public function getActiveFeatureCount(): int
    {
        return $this->data['activeFeaturesCount'];
    }

    public function getFeatureCount(): int
    {
        return $this->data['totalFeatureCount'];
    }
}
