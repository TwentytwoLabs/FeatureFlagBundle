<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use TwentytwoLabs\FeatureFlagBundle\Attribute\Feature;

class DefaultController
{
    #[Feature(name: 'foo')]
    #[Feature(name: 'foo', enabled: false)]
    public function attributeFooError(): Response
    {
        return new Response('DefaultController::attributeFooErrorAction');
    }

    #[Feature(name: 'foo')]
    public function attributeFooEnabled(): Response
    {
        return new Response('DefaultController::attributeFooEnabledAction');
    }
}
