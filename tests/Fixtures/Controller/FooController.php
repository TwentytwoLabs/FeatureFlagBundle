<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use TwentytwoLabs\FeatureFlagBundle\Attribute\IsFeatureDisabled;
use TwentytwoLabs\FeatureFlagBundle\Attribute\IsFeatureEnabled;

#[IsFeatureEnabled(name: 'foo')]
#[IsFeatureDisabled(name: 'bar')]
class FooController
{
    public function __invoke(): Response
    {
        return new Response('FooController::response');
    }
}
