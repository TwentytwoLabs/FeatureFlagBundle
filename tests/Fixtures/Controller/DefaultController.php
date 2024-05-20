<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use TwentytwoLabs\FeatureFlagBundle\Attribute\IsFeatureEnabled;
use TwentytwoLabs\FeatureFlagBundle\Attribute\IsFeatureDisabled;

class DefaultController
{
    #[IsFeatureEnabled(name: 'foo')]
    #[IsFeatureDisabled(name: 'foo')]
    public function attributeFooError(): Response
    {
        return new Response('DefaultController::attributeFooErrorAction');
    }

    #[IsFeatureEnabled(name: 'foo')]
    public function attributeFooEnabled(): Response
    {
        return new Response('DefaultController::attributeFooEnabledAction');
    }
}
