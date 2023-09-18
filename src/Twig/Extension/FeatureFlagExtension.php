<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Twig\Extension;

use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FeatureFlagExtension extends AbstractExtension
{
    private ChainedFeatureManager $manager;

    public function __construct(ChainedFeatureManager $manager)
    {
        $this->manager = $manager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isFeatureEnabled', $this->manager->isEnabled(...)),
            new TwigFunction('isFeatureDisabled', $this->manager->isDisabled(...)),
        ];
    }

    public function getName(): string
    {
        return 'feature_flag_extension';
    }
}
